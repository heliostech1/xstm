<?php
namespace App\Http\Controllers\Vehicle;

use App;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\Vehicle\Vehicle;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Models\Common\OdometerHistory;
use App\Http\Libraries\BigPage\VehicleHelper;
use App\Http\Libraries\ProvinceHelper;
use App\Http\Libraries\BigPage\VehInsuranceHelper;
use App\Http\Models\Vehicle\VehInsurance;

class VehInsuranceController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "vehicle";
    public $criteriaNames = array( "vehicleId","licensePlate","active",
        "actCompany","carCompany","goodsCompany");
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index(Request $request)
    {
        if (!isset($_GET["keep"])) {
            $this->clearCache($request);
        }

        $this->data['message'] = $request->session()->has("message")? $request->session()->get("message"):"";
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();
        $this->data['defaultActive'] = Rdb::$YES;
        
        $this->setCriteriaDatas($request);
        $this->setCriteriaDefaultData('active',  $this->data['defaultActive']);

        $this->data['claimTypeOpt'] = DropdownMgr::getClaimTypeArray(); 
        $this->data['licensePlateList'] = DropdownMgr::getLicensePlateList();  
        
        
        return $this->openView('vehInsurance.listVehInsurance', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = Vehicle::getDataTable($request, $this->getCriteriaDatas($request));

        return response()->json($output);
    }
    
    
    //=======================================================
 
    function getFieldLabels() {
        return [ 'vehicleId' => 'รหัส','province' => 'จังหวัด', 'licensePlate' => 'ทะเบียนรถ', 'status' => 'สถานะ' ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');
        $this->data['vehicleId'] = $request->input('vehicleId');        
        $this->data['licensePlate'] = $request->input('licensePlate');   
        $this->data['active'] = $request->input('active');
      
        
        $this->data = array_merge( 
                $this->data ,
                VehicleHelper::collectRequest($request, "partInsAct_", array(), VehInsuranceHelper::getInsActArrayFields()) ,
                VehicleHelper::collectRequest($request, "partInsCar_", array(), VehInsuranceHelper::getInsCarArrayFields()) ,
                VehicleHelper::collectRequest($request, "partInsGoods_", array(), VehInsuranceHelper::getInsGoodsArrayFields()) ,
                VehicleHelper::collectRequest($request, "partClaim_", array(), VehInsuranceHelper::getClaimArrayFields())                 
        );
        
        $this->data['valueDatas'] = "[]";
        $this->data['fileDatas'] = "[]";         
    }
    
   function _collectDefaultData($request, $result=null) {
        $mongoId = $request->input('mongoId');   
        
        if (empty($result)) {
            $result = (!empty($mongoId))? Vehicle::getData($mongoId): null;
        }

        $partRegis = getMyProp($result, "partRegis_", ""); 
        $regisDatas = getMyProp($partRegis, "regisDatas", ""); 

        $regisItem = (is_array($regisDatas) && sizeof($regisDatas) > 0) ? $regisDatas[ (sizeof($regisDatas) - 1)  ] : null;
        
        $this->data['defaultLicensePlate'] = getMyProp($result, "licensePlate", "");        
        $this->data['defaultCarName'] = getMyProp($regisItem, 'brand', '');            
        $this->data['defaultCarBody'] = getMyProp($regisItem, 'bodyNumber', '');       
        
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();  
        $this->data['yesNoOpt'] = DropdownMgr::getYesNoArray(); 
        $this->data['claimTypeOpt'] = DropdownMgr::getClaimTypeArray(); 
        
    }         
    
    
 
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? Vehicle::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($result)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {
            $this->data['mongoId'] = getMyProp($result, "mongoId", "");          
            $this->data['vehicleId'] = getMyProp($result, "vehicleId", "");
            $this->data['licensePlate'] = getMyProp($result, "licensePlate", "");
            $this->data['province'] = getMyProp($result, "province", "");
            $this->data['odometer'] =  getMyProp($result, "odometer", "");
            $this->data['active'] =  getMyProp($result, "active", "");
     
            $this->data = array_merge( 
                    $this->data ,
                    VehicleHelper::getResultForGet($result, "partInsAct_", array(), VehInsuranceHelper::getInsActArrayFields()) ,
                    VehicleHelper::getResultForGet($result, "partInsCar_", array(), VehInsuranceHelper::getInsCarArrayFields()) ,
                    VehicleHelper::getResultForGet($result, "partInsGoods_", array(), VehInsuranceHelper::getInsGoodsArrayFields()) ,
                    VehicleHelper::getResultForGet($result, "partClaim_", array(), VehInsuranceHelper::getClaimArrayFields())                     
            );        
        }
        
        $this->_collectDefaultData($request, $result);        
    }
    

    function add(Request $request)
    {

    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('vehInsurance.addVehInsurance', $this->data, array("fileUpload"=>true));
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('vehInsurance.addVehInsurance', $this->data, array("fileUpload"=>true));    
    }

    
    function addSubmit(Request $request) {

 
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
        ],[], $this->getFieldLabels());
        
       // $fileDatas = json_decode($request->input('fileDatas'));
            
        if (!$validator->fails())
        {            
            $mongoId = $request->input('mongoId');
            $inputDatas = array();
            $inputDatas = $this->collectPartDataForSave($request, $inputDatas);

            if ( VehInsurance::editData($mongoId, $inputDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("vehInsurance/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, VehInsurance::getErrors());
        $this->_collectPostData($request);
        $this->_collectDefaultData($request);
     
       // $this->data['fileDatas'] = json_encode($fileDatas);           
        $this->data['pageMode'] = 'edit';
        return $this->openView('vehInsurance.addVehInsurance', $this->data, array("fileUpload"=>true));
    }    
    
    
    private function collectPartDataForSave($request, $inputDatas) {
        if ($this->hasPermission('vehInsurance/editInsAct')) {
            $inputDatas['partInsAct_'] = VehicleHelper::getRequestForSave($request,"partInsAct_", array() , VehInsuranceHelper::getInsActArrayFields());
        }
        
        if ($this->hasPermission('vehInsurance/editInsCar')) {
            $inputDatas['partInsCar_'] = VehicleHelper::getRequestForSave($request,"partInsCar_", array() , VehInsuranceHelper::getInsCarArrayFields());    
        }
        
        if ($this->hasPermission('vehInsurance/editInsGoods')) {
            $inputDatas['partInsGoods_'] = VehicleHelper::getRequestForSave($request,"partInsGoods_", array() , VehInsuranceHelper::getInsGoodsArrayFields());
        }
        
        if ($this->hasPermission('vehInsurance/editClaim')) {
            $inputDatas['partClaim_'] = VehicleHelper::getRequestForSave($request,"partClaim_", array() , VehInsuranceHelper::getClaimArrayFields());
        }
        

        return $inputDatas;
    }
    
    
    private function hasPermission($page = '') {
        return App::make("AuthMgr")->hasPagePermission($page);
    }
    
    
    function delete(Request $request)
    {
        $this->cache($request);

    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}







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
use App\Http\Models\Alarm\MonitorPlan;
use App\Http\Libraries\BigPage\VehMonitorHelper;

class VehicleController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "vehicle";
    public $criteriaNames = array( "vehicleId","active", "fuel",
        "licensePlate", "brand","regisDateFrom", "regisDateTo", "ownerName","containerType",
        "vCareType","oilType", "gasType","vehicleCare" ,"bodyNumber",
        "engineNumber", "taxDueDateFrom", "taxDueDateTo", "gasExpDateFrom", "gasExpDateTo"
        
     );
    
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
  
        $this->data['fuelOpt'] = DropdownMgr::getFuelArray(); 
        $this->data['fuelOilOpt'] = DropdownMgr::getFuelOilArray(); 
        $this->data['fuelGasOpt'] = DropdownMgr::getFuelGasArray(); 
        $this->data['goodsContainerOpt'] = DropdownMgr::getGoodsContainerArray(); 
        $this->data['refrigerantOpt'] = DropdownMgr::getRefrigerantArray(); 
        $this->data['vehicleCareOpt'] = DropdownMgr::getVehicleCareArray(); 
        $this->data['vCareTypeOpt'] = DropdownMgr::getVCareTypeArray(); 
        $this->data['licensePlateList'] = DropdownMgr::getLicensePlateList();  

        
        return $this->openView('vehicle.listVehicle', $this->data);
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
        $this->data['province'] = $request->input('province');     
        $this->data['oldOdometer'] = $request->input('oldOdometer');          
        $this->data['odometer'] = $request->input('odometer');   
        $this->data['ageYear'] = $request->input('ageYear');         
        $this->data['active'] = $request->input('active');
      
        $this->data = array_merge( 
                $this->data ,
                VehicleHelper::collectRequest($request, "partRegis_", VehicleHelper::getRegisFields(), VehicleHelper::getRegisArrayFields()) ,
                VehicleHelper::collectRequest($request, "partOwner_",  VehicleHelper::getOwnerFields(), VehicleHelper::getOwnerArrayFields()),
                VehicleHelper::collectRequest($request, "partTax_", VehicleHelper::getTaxFields(), VehicleHelper::getTaxArrayFields()),
                VehicleHelper::collectRequest($request, "partContainer_", VehicleHelper::getContainerFields()) ,
                VehicleHelper::collectRequest($request, "partFuel_",  VehicleHelper::getFuelFields(), VehicleHelper::getFuelArrayFields()),
                
                VehicleHelper::collectRequest($request, "partChiller_", VehicleHelper::getChillerFields(), VehicleHelper::getChillerArrayFields()),
                VehicleHelper::collectRequest($request, "partCare_", VehicleHelper::getCareFields(), VehicleHelper::getCareArrayFields()),
                VehicleHelper::collectRequest($request, "partMonitor_", VehicleHelper::getMonitorFields(), VehicleHelper::getMonitorArrayFields()),          
                VehicleHelper::collectRequest($request, "partMonitorView_", VehicleHelper::getMonitorFields(), VehicleHelper::getMonitorArrayFields())                  
        );
        
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();  
        $this->data['yesNoOpt'] = DropdownMgr::getYesNoArray(); 
        $this->data['provinceOpt'] = ProvinceHelper::getNameOpt(); 
        $this->data['fuelOpt'] = DropdownMgr::getFuelArray(); 
        $this->data['fuelOilOpt'] = DropdownMgr::getFuelOilArray(); 
        $this->data['fuelGasOpt'] = DropdownMgr::getFuelGasArray(); 

        $this->data['goodsContainerOpt'] = DropdownMgr::getGoodsContainerArray(); 
        $this->data['refrigerantOpt'] = DropdownMgr::getRefrigerantArray(); 
        $this->data['vehicleCareOpt'] = DropdownMgr::getVehicleCareArray(); 
        $this->data['vCareTypeOpt'] = DropdownMgr::getVCareTypeArray(); 
        
        $this->data['workCompanyOpt'] = DropdownMgr::getWorkCompanyArray(); 
        $this->data['staffTypeOpt'] = DropdownMgr::getStaffTypeArray(); 
        
        $this->data['valueDatas'] = "[]";
        $this->data['fileDatas'] = "[]";         
        
        $this->data['monitorPlanOpt'] = DropdownMgr::getMonitorPlanArray();  
        $this->data['monitorPlanDatas'] = json_encode( MonitorPlan::getAllDataForView() );  
        
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
            $this->data['oldOdometer'] =  getMyProp($result, "odometer", "");
            $this->data['odometer'] =  getMyProp($result, "odometer", "");
            $this->data['ageYear'] =  getMyProp($result, "ageYear", "");            
            $this->data['active'] =  getMyProp($result, "active", "");
            $this->data['fileDatas'] = isset($result['fileDatas'])? json_encode($result['fileDatas']): "[]";   

            $this->data = array_merge( 
                    $this->data ,
                    VehicleHelper::getResultForGet($result, "partRegis_", VehicleHelper::getRegisFields(), VehicleHelper::getRegisArrayFields()) ,
                    VehicleHelper::getResultForGet($result,  "partOwner_",  VehicleHelper::getOwnerFields(), VehicleHelper::getOwnerArrayFields()),
                    VehicleHelper::getResultForGet($result,  "partTax_", VehicleHelper::getTaxFields(), VehicleHelper::getTaxArrayFields()),
                    VehicleHelper::getResultForGet($result, "partContainer_", VehicleHelper::getContainerFields()) ,
                    VehicleHelper::getResultForGet($result, "partFuel_",  VehicleHelper::getFuelFields(), VehicleHelper::getFuelArrayFields()),
                
                    VehicleHelper::getResultForGet($result, "partChiller_", VehicleHelper::getChillerFields(), VehicleHelper::getChillerArrayFields()),
                    VehicleHelper::getResultForGet($result, "partCare_", VehicleHelper::getCareFields(), VehicleHelper::getCareArrayFields()) ,    
                    VehicleHelper::getResultForGet($result, "partMonitor_", VehicleHelper::getMonitorFields(), VehicleHelper::getMonitorArrayFieldsForView()),         
                    VehMonitorHelper::getMonitorDatasForView($result)
            );        
                        
            // update ขอ้มูล partCare_driverDatas, partCare_workerDatas  => staffName, workCompany ล่าสุด ???
        }
    }
    
              
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('vehicle.addVehicle', $this->data, array("fileUpload"=>true));
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('vehicle.addVehicle', $this->data, array("fileUpload"=>true));
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('vehicle.addVehicle', $this->data, array("fileUpload"=>true));    
    }

    
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
                'licensePlate' => 'required',
        ],[], $this->getFieldLabels());
        
       // $fileDatas = json_decode($request->input('fileDatas'));
        
        if (!$validator->fails())
        {                      
            $inputDatas = array(
                'licensePlate' => $request->input('licensePlate'),
                'province' => $request->input('province'),
                'odometer' => $request->input('odometer'),           
            );   

            $inputDatas = $this->collectPartDataForSave($request, $inputDatas);
            
            if ( Vehicle::addData($inputDatas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลใหม่แล้ว");
                return redirect("vehicle/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, Vehicle::getErrors());
        $this->_collectPostData($request);
       // $this->data['fileDatas'] = json_encode($fileDatas);           
        $this->data['pageMode'] = 'add';
        
        return $this->openView('vehicle.addVehicle', $this->data, array("fileUpload"=>true));
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
                'licensePlate' => 'required',
              //  'description' => 'required|max:1000',
        ],[], $this->getFieldLabels());
        
       // $fileDatas = json_decode($request->input('fileDatas'));
        
        if (!$validator->fails())
        {            
            $mongoId = $request->input('mongoId');
            $vehicleId = $request->input('vehicleId');
            
            $inputDatas = array(
                'licensePlate' => $request->input('licensePlate'),
                'province' => $request->input('province'),
                'odometer' => $request->input('odometer'),
                'active' => $request->input('active'),           
            );
                    
            $inputDatas = $this->collectPartDataForSave($request, $inputDatas);

            if ( Vehicle::editData($mongoId, $inputDatas, $vehicleId)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("vehicle/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, Vehicle::getErrors());
        $this->_collectPostData($request);
       // $this->data['fileDatas'] = json_encode($fileDatas);           
        $this->data['pageMode'] = 'edit';
        return $this->openView('vehicle.addVehicle', $this->data, array("fileUpload"=>true));
    }    
    
    
    private function collectPartDataForSave($request, $inputDatas) {
        
        $mongoId = $request->input('mongoId');
        $oldData = Vehicle::getData($mongoId);
                
        if ($this->hasPermission('vehicle/editRegis')) {
            $inputDatas['partRegis_'] = VehicleHelper::getRequestForSave($request,"partRegis_", VehicleHelper::getRegisFields() , VehicleHelper::getRegisArrayFields());
        }
        
        if ($this->hasPermission('vehicle/editOwner')) {
            $inputDatas['partOwner_'] = VehicleHelper::getRequestForSave($request,"partOwner_", VehicleHelper::getOwnerFields() , VehicleHelper::getOwnerArrayFields());      
        }
        
        if ($this->hasPermission('vehicle/editTax')) {
            $inputDatas['partTax_'] = VehicleHelper::getRequestForSave($request,"partTax_", VehicleHelper::getTaxFields() , VehicleHelper::getTaxArrayFields()); 
        }
        
        if ($this->hasPermission('vehicle/editContainer')) {
            $inputDatas['partContainer_'] = VehicleHelper::getRequestForSave($request, "partContainer_", VehicleHelper::getContainerFields()); 
        }
        
        if ($this->hasPermission('vehicle/editFuel')) {
            $inputDatas['partFuel_'] = VehicleHelper::getRequestForSave($request, "partFuel_",  VehicleHelper::getFuelFields(), VehicleHelper::getFuelArrayFields()); 
        }      
        
        if ($this->hasPermission('vehicle/editChiller')) {
            $inputDatas['partChiller_'] = VehicleHelper::getRequestForSave($request, "partChiller_", VehicleHelper::getChillerFields(), VehicleHelper::getChillerArrayFields()); 
        }
        
        if ($this->hasPermission('vehicle/editCare')) {
            $inputDatas['partCare_'] = VehicleHelper::getRequestForSave($request, "partCare_", VehicleHelper::getCareFields(), VehicleHelper::getCareArrayFields()); 
        }
        
        if ($this->hasPermission('vehicle/editMonitor')) {
            $newPartMonitor  = VehicleHelper::getRequestForSave($request, "partMonitor_", VehicleHelper::getMonitorFields(), VehicleHelper::getMonitorArrayFields()); 
            $inputDatas['partMonitor_'] = VehMonitorHelper::mergePartMonitorWithOldData( $newPartMonitor, $oldData );
        }
        
        $inputDatas['ageYear'] = VehicleHelper::getAgeYear($inputDatas);
        $inputDatas['workCompany'] = VehicleHelper::getWorkCompany($inputDatas);
               
        return $inputDatas;
    }
    
    
    private function hasPermission($page = '') {
        return App::make("AuthMgr")->hasPagePermission($page);
    }
    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $object = Vehicle::getData($paramId);
        
        if (!$object) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            if (Vehicle::deleteData($paramId)) {
                $request->session()->flash('message', "ลบข้อมูล '".$object['licensePlate']."' แล้ว");
            }
            else {
                $request->session()->flash('message', Vehicle::errors());
            }
        }
         
        return redirect("vehicle/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}







<?php
namespace App\Http\Controllers\Vehicle;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\Vehicle\VehAccident;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DateHelper;

class VehAccidentController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "vehAccident";
    public $criteriaNames = array( "vehicleId","licensePlate","active","accDateFrom","accDateTo");
    
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
        $this->data['vehicleOpt'] = DropdownMgr::getVehicleArray();  
        $this->data['licensePlateList'] = DropdownMgr::getLicensePlateList();  
        
        $this->setCriteriaDatas($request);

        return $this->openView('vehAccident.listVehAccident', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = VehAccident::getDataTable($request, $this->getCriteriaDatas($request));

        
        return response()->json($output);
    }
    

    //=======================================================
 
    function getFieldLabels() {
        return [ 'vehicleId' => 'รหัสรถ','licensePlate' => 'ทะเบียนรถ', 'accDate' => 'วันที่เกิดเหตุ' ];
    }
    

    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');
        $this->data['vehicleId'] = $request->input('vehicleId');
        $this->data['licensePlate'] = $request->input('licensePlate');        
        $this->data['times'] = $request->input('times');     
        $this->data['accDate'] = $request->input('accDate');    
        
        $this->data['accPlace'] = $request->input('accPlace');   
        $this->data['driverName'] = $request->input('driverName');   
        $this->data['fixStartDate'] = $request->input('fixStartDate');   
        $this->data['fixEndDate'] = $request->input('fixEndDate');  
        $this->data['cost'] = $request->input('cost');  

        $this->data['fileDatas'] = $request->input('fileDatas');  
        
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();  
        $this->data['yesNoOpt'] = DropdownMgr::getYesNoArray(); 
        $this->data['vehicleOpt'] = DropdownMgr::getVehicleArray();  
        
        $this->data['valueDatas'] = "[]";      
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? VehAccident::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($result)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {            
        
            $this->data['mongoId'] = getMyProp($result, "mongoId", "");
            $this->data['vehicleId'] = getMyProp($result, "vehicleId", "");
            $this->data['licensePlate'] = getMyProp($result, "licensePlate", "");
            $this->data['times'] =  getMyProp($result, "times", "");
            $this->data['accDate'] = DateHelper::mongoDateToThai(getMyProp($result, "accDate", ""), false) ;
            
            $this->data['accPlace'] = getMyProp($result, "accPlace", "");
            $this->data['driverName'] = getMyProp($result, "driverName", "");            
            $this->data['fixStartDate'] = DateHelper::mongoDateToThai( getMyProp($result, "fixStartDate", ""), false) ;   
            $this->data['fixEndDate'] = DateHelper::mongoDateToThai( getMyProp($result, "fixEndDate", ""), false) ;  
            $this->data['cost'] = getMyProp($result, "cost", "");   
            
            $this->data['fileDatas'] = getMyProp($result, "fileDatas", "");               
           // $this->data['fileDatas'] = isset($result['fileDatas'])? json_encode($result['fileDatas']): "[]";   
        
        }
    }
    
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('vehAccident.addVehAccident', $this->data, array("fileUpload"=>true));
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('vehAccident.addVehAccident', $this->data, array("fileUpload"=>true));
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('vehAccident.addVehAccident', $this->data, array("fileUpload"=>true));
    
    }
    

        
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
                'vehicleId' => 'required',
                'accDate' => 'required',
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {
            $inputDatas = array(
                'vehicleId' => $request->input('vehicleId'),
                'times' => $request->input('times'),
                'accDate' => DateHelper::thaiToMongoDate( $request->input('accDate') ),
                'accPlace' => $request->input('accPlace'),
                'driverName' => $request->input('driverName'),   
                
                'fixStartDate' => DateHelper::thaiToMongoDate( $request->input('fixStartDate')),
                'fixEndDate' => DateHelper::thaiToMongoDate( $request->input('fixEndDate')),           
                'cost' => $request->input('cost'),                  
                'fileDatas' => $request->input('fileDatas'),                  
            );   
            
            if ( VehAccident::addData($inputDatas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลใหม่แล้ว");
                return redirect("vehAccident/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, VehAccident::getErrors());
        $this->_collectPostData($request);      
        $this->data['pageMode'] = 'add';
        
        return $this->openView('vehAccident.addVehAccident', $this->data, array("fileUpload"=>true));
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
                'vehicleId' => 'required',
                'accDate' => 'required',            
        ],[], $this->getFieldLabels());

        
        if (!$validator->fails())
        {            
            $mongoId = $request->input('mongoId');
            $inputDatas = array(
                'vehicleId' => $request->input('vehicleId'),
                'times' => $request->input('times'),
                'accDate' => DateHelper::thaiToMongoDate( $request->input('accDate') ),
                'accPlace' => $request->input('accPlace'),
                'driverName' => $request->input('driverName'),   
                
                'fixStartDate' => DateHelper::thaiToMongoDate( $request->input('fixStartDate')),
                'fixEndDate' => DateHelper::thaiToMongoDate( $request->input('fixEndDate')),           
                'cost' => $request->input('cost'),                  
                'fileDatas' => $request->input('fileDatas'),                    
            );
                       
            if ( VehAccident::editData($mongoId, $inputDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("vehAccident/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, VehAccident::getErrors());
        $this->_collectPostData($request);         
        $this->data['pageMode'] = 'edit';
        return $this->openView('vehAccident.addVehAccident', $this->data, array("fileUpload"=>true));
    }    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $object = VehAccident::getData($paramId);
        
        if (!$object) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            $licensePlate = getMyProp($object, 'licensePlate', '');
            if (VehAccident::deleteData($paramId)) {
                $request->session()->flash('message', "ลบข้อมูล '".$licensePlate."' แล้ว");
            }
            else {
                $request->session()->flash('message', VehAccident::errors());
            }
        }
         
        return redirect("vehAccident/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}







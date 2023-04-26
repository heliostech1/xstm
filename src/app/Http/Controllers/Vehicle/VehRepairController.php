<?php
namespace App\Http\Controllers\Vehicle;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\Vehicle\VehRepair;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Alarm\MonitorTopic;

class VehRepairController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "vehRepair";
    public $criteriaNames = array( "vehicleId","licensePlate","active","fixDateFrom","fixDateTo");
    
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

        return $this->openView('vehRepair.listVehRepair', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = VehRepair::getDataTable($request, $this->getCriteriaDatas($request));

        
        return response()->json($output);
    }
    

    //=======================================================
 
    function getFieldLabels() {
        return [ 'vehicleId' => 'รหัสรถ','licensePlate' => 'ทะเบียนรถ', 'fixStartDate' => 'วันที่ซ่อมบำรุง' 
            , 'fixEndDate' => 'วันที่ซ่อมเสร็จ' ];
    }
    

    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');
        $this->data['vehicleId'] = $request->input('vehicleId');
        $this->data['licensePlate'] = $request->input('licensePlate');        
        $this->data['times'] = $request->input('times');     
        $this->data['fixStartDate'] = $request->input('fixStartDate');   
        
        $this->data['fixEndDate'] = $request->input('fixEndDate');    
        $this->data['odometer'] = $request->input('odometer');          
        $this->data['fixItemDatas'] = $request->input('fixItemDatas'); 
        $this->data['cost'] = $request->input('cost');  
        $this->data['guaranty'] = $request->input('guaranty'); 
        
        $this->data['fileDatas'] = $request->input('fileDatas');  
        $this->data['monitorTopicDatas'] = $request->input('monitorTopicDatas'); 
        $this->data['repairGroup'] = $request->input('repairGroup'); 
        
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();  
        $this->data['yesNoOpt'] = DropdownMgr::getYesNoArray(); 
        $this->data['vehicleOpt'] = DropdownMgr::getVehicleArray();  
        $this->data['repairGroupOpt'] = DropdownMgr::getRepairGroupArray();  
        
        $this->data['valueDatas'] = "[]";      
        
        $this->data['allMonitorTopicDatas'] = json_encode( MonitorTopic::getDataTableForView() );  
        
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? VehRepair::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($result)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {            

        
        
            $this->data['mongoId'] = getMyProp($result, "mongoId", "");
            $this->data['vehicleId'] = getMyProp($result, "vehicleId", "");
            $this->data['licensePlate'] = getMyProp($result, "licensePlate", "");
            $this->data['times'] =  getMyProp($result, "times", "");          
            $this->data['fixStartDate'] = DateHelper::mongoDateToThai( getMyProp($result, "fixStartDate", ""), false) ;   
            
            $this->data['fixEndDate'] = DateHelper::mongoDateToThai( getMyProp($result, "fixEndDate", ""), false) ;   
            $this->data['odometer'] = getMyProp($result, "odometer", "");            
            $this->data['fixItemDatas'] = getMyProp($result, "fixItemDatas", "");               
            $this->data['cost'] = getMyProp($result, "cost", "");   
            $this->data['guaranty'] = getMyProp($result, "guaranty", "");   
            
            $this->data['fileDatas'] = getMyProp($result, "fileDatas", "");             
            $this->data['repairGroup'] = getMyProp($result, "repairGroup", ""); 
            $this->data['monitorTopicDatas'] = getMyProp($result, "monitorTopicDatas", ""); 
  
            
           // $this->data['fileDatas'] = isset($result['fileDatas'])? json_encode($result['fileDatas']): "[]";   
        
        }
    }
    
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('vehRepair.addVehRepair', $this->data, array("fileUpload"=>true));
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('vehRepair.addVehRepair', $this->data, array("fileUpload"=>true));
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('vehRepair.addVehRepair', $this->data, array("fileUpload"=>true));
    
    }
    

        
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
                'vehicleId' => 'required',
                'fixStartDate' => 'required',
                'fixEndDate' => 'required',
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {

            $inputDatas = array(
                'vehicleId' => $request->input('vehicleId'),
                'fixStartDate' => DateHelper::thaiToMongoDate( $request->input('fixStartDate') ),
                'fixEndDate' => DateHelper::thaiToMongoDate( $request->input('fixEndDate') ),
                'odometer' => $request->input('odometer'),                
                'fixItemDatas' => $request->input('fixItemDatas'),  
                
                'cost' => $request->input('cost'),
                'guaranty' => $request->input('guaranty'),                           
                'fileDatas' => $request->input('fileDatas'),    
                'repairGroup' => $request->input('repairGroup'),  
                'monitorTopicDatas' => $request->input('monitorTopicDatas'),  
            );   
            
            if ( VehRepair::addData($inputDatas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลใหม่แล้ว");
                return redirect("vehRepair/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, VehRepair::getErrors());
        $this->_collectPostData($request);      
        $this->data['pageMode'] = 'add';
        
        return $this->openView('vehRepair.addVehRepair', $this->data, array("fileUpload"=>true));
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
                'vehicleId' => 'required',
                'fixStartDate' => 'required',    
                'fixEndDate' => 'required',            
        ],[], $this->getFieldLabels());

        
        if (!$validator->fails())
        {            
            $mongoId = $request->input('mongoId');
            $inputDatas = array(
                'vehicleId' => $request->input('vehicleId'),
                'fixStartDate' => DateHelper::thaiToMongoDate( $request->input('fixStartDate') ),
                'fixEndDate' => DateHelper::thaiToMongoDate( $request->input('fixEndDate') ),
                'odometer' => $request->input('odometer'),                
                'fixItemDatas' => $request->input('fixItemDatas'),  
                
                'cost' => $request->input('cost'),
                'guaranty' => $request->input('guaranty'),                           
                'fileDatas' => $request->input('fileDatas'),      
                'repairGroup' => $request->input('repairGroup'),  
                'monitorTopicDatas' => $request->input('monitorTopicDatas'),                  
            );
                       
            if ( VehRepair::editData($mongoId, $inputDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("vehRepair/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, VehRepair::getErrors());
        $this->_collectPostData($request);         
        $this->data['pageMode'] = 'edit';
        return $this->openView('vehRepair.addVehRepair', $this->data, array("fileUpload"=>true));
    }    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $object = VehRepair::getData($paramId);
        
        if (!$object) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            $licensePlate = getMyProp($object, 'licensePlate', '');
            if (VehRepair::deleteData($paramId)) {
                $request->session()->flash('message', "ลบข้อมูล '".$licensePlate."' แล้ว");
            }
            else {
                $request->session()->flash('message', VehRepair::errors());
            }
        }
         
        return redirect("vehRepair/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}







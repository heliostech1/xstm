<?php
namespace App\Http\Controllers\Alarm;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\Alarm\MonitorPlan;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;

class MonitorPlanController extends MyBaseController
{

    public $criteriaPrefix = "monitorPlan";
    public $criteriaNames = array("name","active","date","toDate");
    
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
        
        $this->setCriteriaDatas($request);

        return $this->openView('monitorPlan.listMonitorPlan', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = MonitorPlan::getDataTable($request, $this->getCriteriaDatas($request));
        
        //DataHelper::debug($output);
        
        return response()->json($output);
    }
    

    //=======================================================
 
    function getFieldLabels() {
        return [ 'name' => 'ชื่อ', 'status' => 'สถานะ' ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');
        $this->data['name'] = $request->input('name');     
        $this->data['active'] = $request->input('active');

        $this->data['activeOpt'] = DropdownMgr::getActiveArray();       
        
        $this->data['monitorTopicOpt'] = DropdownMgr::getMonitorTopicArray();
        $this->data['monitorDataTypeOpt'] = DropdownMgr::getMonitorDataTypeArray();
        
        $this->data['valueDatas'] = "[]";
        $this->data['fileDatas'] = "[]";        
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? MonitorPlan::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($result)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {
            $this->data['mongoId'] = DataHelper::getMyProp($result, "mongoId", "");
            $this->data['name'] = DataHelper::getMyProp($result, "name", "");
            $this->data['active'] =  DataHelper::getMyProp($result, "active", "");
            $this->data['valueDatas'] = isset($result['detailDatas'])? json_encode($result['detailDatas']): "[]";   
        
        }
    }
    
    function _getDataForAdd($request) {
        $addByCopy = $request->input("addByCopy");  
        $this->_collectPostData($request);

        if ($addByCopy == Rdb::$YES) {
            $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
            $result = (!empty($paramId))? MonitorPlan::getData($paramId): null;

            if (empty($result)) {
                $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
            }
            else {
                $this->data['valueDatas'] = isset($result['detailDatas'])? json_encode($result['detailDatas']): "[]";   
            }           
        }
    }
    
    function add(Request $request)
    {
        $this->cache($request);
        $this->_getDataForAdd($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('monitorPlan.addMonitorPlan', $this->data, array("fileUpload"=>true));
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('monitorPlan.addMonitorPlan', $this->data, array("fileUpload"=>true));
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('monitorPlan.addMonitorPlan', $this->data, array("fileUpload"=>true));
    
    }
    
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
                'name' => 'required|max:1000',
        ],[], $this->getFieldLabels());
        
        $valueDatas = json_decode($request->input('valueDatas'));
        
        if (!$validator->fails())
        {
            $inputDatas = array(
                'name' => $request->input('name'),
            );   
            
            if ( MonitorPlan::addData($inputDatas, $valueDatas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลใหม่แล้ว");
                return redirect("monitorPlan/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, MonitorPlan::getErrors());
        $this->_collectPostData($request);
        $this->data['valueDatas'] = json_encode($valueDatas);            
        $this->data['pageMode'] = 'add';
        
        return $this->openView('monitorPlan.addMonitorPlan', $this->data, array("fileUpload"=>true));
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
                'name' => 'required|max:1000',
        ],[], $this->getFieldLabels());
        
        $valueDatas = json_decode($request->input('valueDatas'));
        
        if (!$validator->fails())
        {            
            $mongoId = $request->input('mongoId');
            $inputDatas = array(
                'name' => $request->input('name'),
                'active' => $request->input('active'),                   
            );
                       
            if ( MonitorPlan::editData($mongoId, $inputDatas, $valueDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("monitorPlan/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, MonitorPlan::getErrors());
        $this->_collectPostData($request);
        $this->data['valueDatas'] = json_encode($valueDatas);            
        $this->data['pageMode'] = 'edit';
        return $this->openView('monitorPlan.addMonitorPlan', $this->data, array("fileUpload"=>true));
    }    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $object = MonitorPlan::getData($paramId);
        
        if (!$object) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            if (MonitorPlan::deleteData($paramId)) {
                $request->session()->flash('message', "ลบข้อมูล '".$object['name']."' แล้ว");
            }
            else {
                $request->session()->flash('message', MonitorPlan::errors());
            }
        }
         
        return redirect("monitorPlan/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}







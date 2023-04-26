<?php
namespace App\Http\Controllers\Common;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\Common\WorkSite;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;

class WorkSiteController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "workSite";
    public $criteriaNames = array( "work_site_id","name","active","date","toDate");
    
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

        return $this->openView('workSite.listWorkSite', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = WorkSite::getDataTable($request, $this->getCriteriaDatas($request));
        
        //DataHelper::debug($output);
        
        return response()->json($output);
    }
    

    //=======================================================
 
    function getFieldLabels() {
        return [ 'work_site_id' => 'รหัส','name' => 'ชื่อ', 'status' => 'สถานะ' ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');
        $this->data['work_site_id'] = $request->input('work_site_id');
        $this->data['name'] = $request->input('name');     
        $this->data['title'] = $request->input('title');     
        $this->data['surname'] = $request->input('surname');     
        $this->data['length'] = $request->input('length');         
        
        $this->data['active'] = $request->input('active');

        $this->data['activeOpt'] = DropdownMgr::getActiveArray();           
        $this->data['valueDatas'] = "[]";
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? WorkSite::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($result)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {
            $this->data['mongoId'] = DataHelper::getMyProp($result, "mongoId", "");
            $this->data['work_site_id'] = DataHelper::getMyProp($result, "work_site_id", "");
            $this->data['name'] = DataHelper::getMyProp($result, "name", "");
            $this->data['active'] =  DataHelper::getMyProp($result, "active", "");
            $this->data['title'] = DataHelper::getMyProp($result, "title", "");
            $this->data['surname'] = DataHelper::getMyProp($result, "surname", "");

        
        }
    }
    
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('workSite.addWorkSite', $this->data);
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('workSite.addWorkSite', $this->data);
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('workSite.addWorkSite', $this->data);
    
    }
    
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
              //  'work_site_id' => 'required',
                'name' => 'required|max:1000',
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {
            $inputDatas = array(
            //    'work_site_id' => $request->input('work_site_id'),
                'name' => $request->input('name'),
            //    'title' => $request->input('title'),
            //    'surname' => $request->input('surname'),
            );   
            
            if ( WorkSite::addData($inputDatas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลใหม่แล้ว");
                return redirect("workSite/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, WorkSite::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';
        
        return $this->openView('workSite.addWorkSite', $this->data);
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
            //    'work_site_id' => 'required',
                'name' => 'required|max:1000',
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {            
            $mongoId = $request->input('mongoId');
            $inputDatas = array(
             //   'work_site_id' => $request->input('work_site_id'),
                'name' => $request->input('name'),
            //    'title' => $request->input('title'),
            //    'surname' => $request->input('surname'),
                'active' => $request->input('active'),                   
            );
                       
            if ( WorkSite::editData($mongoId, $inputDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("workSite/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, WorkSite::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'edit';
        return $this->openView('workSite.addWorkSite', $this->data);
    }    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $object = WorkSite::getData($paramId);
        
        if (!$object) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            if (WorkSite::deleteData($paramId)) {
                $request->session()->flash('message', "ลบข้อมูล '".$object['name']."' แล้ว");
            }
            else {
                $request->session()->flash('message', WorkSite::errors());
            }
        }
         
        return redirect("workSite/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}







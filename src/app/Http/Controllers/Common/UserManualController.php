<?php
namespace App\Http\Controllers\Common;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\Common\UserManual;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;

class UserManualController extends MyBaseController
{

    public $criteriaPrefix = "userManual";
    public $criteriaNames = array( "name","active");
    
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
        $this->data['tableDatas'] = json_encode(UserManual::getDataForView());
        
        $this->setCriteriaDatas($request);

        return $this->openView('userManual.listUserManual', $this->data, array("fileUpload"=>true));
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = array();
        return response()->json($output);
    }
    

    //=======================================================
 
    function getFieldLabels() {
        return [ 'ruleId' => 'รหัส','name' => 'ชื่อ', 'status' => 'สถานะ' ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');
        $this->data['name'] = $request->input('name');     
        $this->data['active'] = $request->input('active');

        $this->data['activeOpt'] = DropdownMgr::getActiveArray();           
        $this->data['valueDatas'] = "[]";
        $this->data['fileDatas'] = "[]";  
    }
    
    function _getDataForViewEdit($request) {
        
        
        $result =  UserManual::getData();
        $this->_collectPostData($request);
    
        $this->data['mongoId'] = DataHelper::getMyProp($result, "keyId", "");
        $this->data['name'] = DataHelper::getMyProp($result, "name", "");
        $this->data['active'] =  DataHelper::getMyProp($result, "active", "");
        $this->data['valueDatas'] = isset($result['detailDatas'])? json_encode($result['detailDatas']): "[]";   
        
    }

    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('userManual.addUserManual', $this->data, array("fileUpload"=>true));
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('userManual.addUserManual', $this->data, array("fileUpload"=>true));
    
    }


    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
        ],[], $this->getFieldLabels());
        
        $valueDatas = json_decode($request->input('valueDatas'));
        
        //myDebug($valueDatas);
        
        if (!$validator->fails())
        {            

            if ( UserManual::editData($valueDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("userManual/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, UserManual::getErrors());
        $this->_collectPostData($request);
        $this->data['valueDatas'] = json_encode($valueDatas);            
        $this->data['pageMode'] = 'edit';
        return $this->openView('userManual.addUserManual', $this->data, array("fileUpload"=>true));

    }    
    


 
}







<?php
namespace App\Http\Controllers\UserGroup;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Http\Models\User;
use App\Http\Models\UserGroup;
use App\Http\Controllers\MyBaseController;
use App;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DropdownMgr;
use Log;
use App\Http\Models\Rdb;
use App\Http\Models\Account;
use App\Http\Models\UserGroupPagePermission;


class UserGroupController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "userGroup";
    public $criteriaNames = array( "userGroupId");
    
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

        $this->setCriteriaDatas($request);

        return $this->openView('userGroup.listUserGroup', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = UserGroup::getDataTable($request, $this->getCriteriaDatas($request));

        return response()->json($output);
    }
    
    function getPagePermissionDataTable(Request $request) {
        $userGroupId = $request->input('userGroupId');
        //$appPlanId = $request->input('app_plan_id');
        
        //DataHelper::debug("XXX $userGroupId");
        
        if (empty($userGroupId)) { //  || empty($appPlanId) 
            return;
        }        
        $output = UserGroupPagePermission::getDataTable($request, $userGroupId); //, $appPlanId
    
        return response()->json($output);
    }
    
    //=======================================================
    
    function getFieldLabels() {
        return [ 'userGroupId' => 'รหัสกลุ่มผู้ใช้','name' => 'ชื่อกลุ่มผู้ใช้', 'user_group_name' => 'ชื่อกลุ่มผู้ใช้', 
                'description' => 'รายละเอียด','color' => 'สี', "result_datas" => 'ข้อมูลสิทธิ์'
        ];
    }
    
    function _collectPostData($request) {
           
        $this->data['userGroupId'] = $request->input('userGroupId');
        $this->data['user_group_name'] = $request->input('user_group_name');
        $this->data['description'] = $request->input('description');
        $this->data['color'] = $request->input('color');
        //$this->data['activeOpt'] = DropdownMgr::getActiveArray();
    
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $userGroup = (!empty($paramId))? UserGroup::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($userGroup)) {
            $this->data['message'] = "ไม่พบรหัส:".$param_id;
        }
        else {
            $this->data['userGroupId'] = isset($userGroup['userGroupId'])? $userGroup['userGroupId']:"";
            $this->data['description'] =  isset($userGroup['description'])? $userGroup['description']:"";
            $this->data['user_group_name'] =  isset($userGroup['name'])? $userGroup['name']:""; 
            $this->data['color'] =  isset($userGroup['color'])? $userGroup['color']:""; 
   
        }
    }
    
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('userGroup.addUserGroup', $this->data);
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('userGroup.addUserGroup', $this->data);
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('userGroup.addUserGroup', $this->data);
    
    }
    
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
                'userGroupId' => 'required|max:50',
                'user_group_name' => 'required',
        ],[], $this->getFieldLabels());
        
        if (!$validator->fails())
        {
            $userGroupId = strtolower($request->input('userGroupId'));
   
            $datas = array(
                'userGroupId' => $userGroupId,
                'name' => $request->input('user_group_name'),
                'description' =>  $request->input('description'),
                'color' =>  $request->input('color'),
            );   
    
            if ( UserGroup::addData($userGroupId, $datas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลแล้ว");
                return redirect("userGroup/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, User::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';
        
        //DataHelper::debug( $this->data, "ERROR:");
        return $this->openView('userGroup.addUserGroup', $this->data);
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'userGroupId' => 'required|max:50',
                'user_group_name' => 'required',
        ],[], $this->getFieldLabels());
        
        if (!$validator->fails())
        {
            $userGroupId = strtolower($request->input('userGroupId'));
   
            $datas = array(
                'name' => $request->input('user_group_name'),
                'description' =>  $request->input('description'),
                'color' =>  $request->input('color'),
            );   
    
            if ( UserGroup::editData($userGroupId, $datas)) {     
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("userGroup/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, User::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'edit';
        
        //DataHelper::debug( $this->data, "ERROR:");
        return $this->openView('userGroup.addUserGroup', $this->data);
    }    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        
        if (!UserGroup::isExist($paramId)) {
            $request->session()->flash('message', "ไม่พบข้อมูล'".$paramId."'");
        }
        else if (UserGroup::deleteData($paramId)) {
            $request->session()->flash('message', "ลบข้อมูล '".$paramId."' แล้ว");
        }
         
        return redirect("userGroup/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // EDIT PAGE PERMISSION
    //
    //====================================================================================
    
    function _collectPagePermissionPostData($request) {

        $this->data['userGroupId'] = $request->input('userGroupId');
        $this->data['app_plan_id'] = $request->input('app_plan_id');
          
        $this->data['allPermissionModes'] = array();//$this->rdb_model->get_permission_mode();
        $this->data['permissionDatas'] = '[]';
        
        $this->data['app_plan_opt'] = DropdownMgr::getAppPlanArray();
        $this->data['userGroupOpt'] = DropdownMgr::getUserGroupArray();        
    }
    
    function viewPagePermission(Request $request) {
        $this->cache($request);
        $this->_collectPagePermissionPostData($request);
    
        $this->data['userGroupId'] = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $this->data['pageMode'] = 'view';
    
        return $this->openView('userGroup.editPagePermission', $this->data);
    }    
    
    function editPagePermission(Request $request) {
        $this->cache($request);
        $this->_collectPagePermissionPostData($request);
    
        $this->data['userGroupId'] = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $this->data['pageMode'] = 'edit';
         
        return $this->openView('userGroup.editPagePermission', $this->data);
    }
    
    
    function editPagePermissionSubmit(Request $request) {
        
        $validator = $this->genValidator($request, [
                'userGroupId' => 'required',
           //     'app_plan_id' => 'required',
                'result_datas' => 'required',
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {
            $userGroupId = strtolower($request->input('userGroupId'));
       //     $appPlanId = $request->input('app_plan_id');
            $resultDatas  = json_decode($request->input('result_datas'));
            
            if (UserGroupPagePermission::updateData($userGroupId, $resultDatas)) { // , $appPlanId
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("userGroup/index?keep=1"); 
            }
        }

        $this->data['message'] = $this->getResponseMessage($request, $validator, UserGroupPagePermission::getErrors());
        $this->_collectPagePermissionPostData($request);
        $this->data['pageMode'] = 'edit';
        
        return $this->openView('userGroup.editPagePermission', $this->data);       
    }
    
}







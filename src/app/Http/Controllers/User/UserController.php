<?php
namespace App\Http\Controllers\User;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Http\Models\User;
use App\Http\Controllers\MyBaseController;
use App;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DropdownMgr;
use Log;
use App\Http\Models\Rdb;
use App\Http\Models\Account;
use App\Http\Models\SiteUsageHistory;

class UserController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "user";
    public $criteriaNames = array( "userId");
    
    public function __construct()
    {
        parent::__construct(true, false);
    }
    
    public function index(Request $request)
    {
        if (!isset($_GET["keep"])) {
            $this->clearCache($request);
        }

        $this->data['message'] = $request->session()->has("message")? $request->session()->get("message"):"";

        $this->setCriteriaDatas($request);
        
        //$users = User::orderBy('createdAt', 'asc')->get();
        //$tasks = $this->tasks->forUser($request->user());

        //DataHelper::debug($this->data);
        
        return $this->openView('user.listUser', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = User::getDataTable($request, $this->getCriteriaDatas($request));
        
        //DataHelper::debug($output);
        
        return response()->json($output);
    }
    
    function getSiteUsageHistoryDataTable(Request $request) {
        $output = SiteUsageHistory::getDataTable($request);

        return response()->json($output);
    }
    
    //=======================================================
    
    function getFieldLabels() {
        return [ 'userId' => 'รหัสผู้ใช้',  'password' => 'รหัสผ่าน', 'password_confirm' => 'ยืนยันรหัสผ่าน',
             'userGroupId' => 'กลุ่มผู้ใช้','description' => 'รายละเอียดผู้ใช้',
             'contactName' => 'ชื่อที่ติดต่อได้','contactPhone' => 'โทรศัพท์ที่ติดต่อได้','contactEmail' => 'อีเมล์ที่ติดต่อได้',
             'old_password' => 'รหัสผ่านเดิม', 'new_password' => 'รหัสผ่านใหม่', 'confirm_new_password' => 'ยืนยันรหัสผ่านใหม่',
             'branchId' => 'สาขา','bookId' => 'สมุดบัญชี',
        ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['userId'] = $request->input('userId');
        $this->data['description'] = $request->input('description');
        $this->data['branchId'] = $request->input('branchId');
        $this->data['userGroupId'] = $request->input('userGroupId');
        $this->data['contactName'] = $request->input('contactName');
        $this->data['contactPhone'] = $request->input('contactPhone');
        $this->data['contactEmail'] = $request->input('contactEmail');
        $this->data['password'] = $request->input('password');
        $this->data['password_confirm'] = $request->input('password_confirm');
        $this->data['old_userId'] = $request->input('old_userId');
        $this->data['active'] = $request->input('active');
        $this->data['change_password'] = (isset($_POST['change_password']))? "checked": "";
        $this->data['bookId'] = $request->input('bookId');
        
        $this->data['user_group_opt'] =  DropdownMgr::getUserGroupArray();
        $this->data['activeOpt'] = DropdownMgr::getActiveArray(false);
        $this->data['branch_opt'] = DropdownMgr::getBranchArray(); 
      //  $this->data['book_opt'] = DropdownMgr::getBookArray();
    
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $user = (!empty($paramId))? User::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($user)) {
            $this->data['message'] = "ไม่พบรหัสผู้ใช้:".$param_id;
        }
        else {
            $this->data['old_userId'] = $paramId;
            $this->data['change_password'] = "";
            $this->data['password'] = "";
    
            $this->data['userId'] = $user['userId'];
            $this->data['description'] =  isset($user['description'])? $user['description']: "";
            $this->data['userGroupId'] =  isset($user['userGroupId'])? $user['userGroupId']: "";
            $this->data['contactName'] =  isset($user['contactName'])? $user['contactName']: "";
            $this->data['contactPhone'] = isset($user['contactPhone'])? $user['contactPhone']: "";
            $this->data['contactEmail'] =  isset($user['contactEmail'])? $user['contactEmail']: "";
            $this->data['active'] =  isset($user['active'])? $user['active']: ""; 
            $this->data['branchId'] =  isset($user['branchId'])? $user['branchId']: "";
            $this->data['bookId'] =  isset($user['bookId'])? $user['bookId']: "";
            
            //if ($user->user_group == $this->rdb_model->user_group_system_admin) { // fixed in database
            //    $this->data['user_group_opt'] = DropdownMgr::getEmptyArray();// $this->dropdown_mgr->get_sysadmin_user_group_array();
            //}    
        }
    }
    
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('user.addUser', $this->data);
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('user.addUser', $this->data);
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('user.addUser', $this->data);
    
    }
    
    function viewSiteUsageHistory(Request $request) {
        $this->cache($request);    
    
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');

        $this->data['criteria_id'] = $paramId;
        
        return $this->openView('user.viewSiteUsageHistory', $this->data);
    }
    
    
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
                'userId' => 'required|max:50',
                'password' => 'required|max:50|min:8',
                'password_confirm' => 'required|same:password',
                'userGroupId' => 'required',
        ],[], $this->getFieldLabels());
        
        if (!$validator->fails())
        {
            $userId = strtolower($request->input('userId'));
            
            $userDatas = array(
                'userId' => $userId,
                'password' => DataHelper::hashPassword( $request->input('password') ),
                'branchId' => $request->input('branchId'),
                'description' => $request->input('description'),
                'userGroupId' => $request->input('userGroupId'),
                'contactName' => $request->input('contactName'),
                'contactPhone' => $request->input('contactPhone'),
                'contactEmail' => $request->input('contactEmail'),
                'bookId' => $request->input('bookId'),
            );   
    
            if ( User::addData($userId, $userDatas)) {     
                $request->session()->flash('message', "เพิ่มผู้ใช้ใหม่แล้ว");
                return redirect("user/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, User::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';
        
        //DataHelper::debug( $this->data, "ERROR:");
        return $this->openView('user.addUser', $this->data);
    }
    
    
    function editSubmit(Request $request) {
    
        $newUserId = strtolower($request->input('userId'));
        $oldUserId = $request->input('old_userId');
        $password = $request->input('password');
        
        $errMessage = "";
 
        if (!User::isExist($oldUserId)) {
            $errMessage .= "<div>ไม่พบรหัสผู้ใช้  '$oldUserId' ในระบบ</div>";
        }
        else if (!empty($newUserId) && ($newUserId != $oldUserId) && User::isExist($newUserId)) {        
            $errMessage .= "<div>รหัสผู้ใช้นี้ '$newUserId' มีอยู่แล้วในระบบ</div>";
        }
            
        $validateRules =  [
            'userId' => 'required|max:50',
            'userGroupId' => 'required',
            //'userGroupId' => '',
        ];
        
        if (isset($_POST['change_password'])) {
            $validateRules['password'] =  'required|max:50|min:8';
            $validateRules['password_confirm'] =  'required|same:password';
        }
        
        $validator = $this->genValidator($request, $validateRules,[], $this->getFieldLabels());
    
        if (empty($errMessage) && !$validator->fails())
        {
            $userDatas = array(
                    'userId' => $newUserId,
                    'active' => $request->input('active'),
                    'description' => $request->input('description'),
                    'userGroupId' => $request->input('userGroupId'),
                    'contactName' => $request->input('contactName'),
                    'contactPhone' => $request->input('contactPhone'),
                    'contactEmail' => $request->input('contactEmail'),
                    'branchId' => $request->input('branchId'),
                    'bookId' => $request->input('bookId'),                    
            );
    
            if (isset($_POST['change_password'])) {
                $userDatas['password'] =  DataHelper::hashPassword( $request->input('password'));
            }
            
            if ( User::editData($oldUserId, $userDatas)) {
                $request->session()->flash('message', "แก้ไขผู้ใช้แล้ว");
                return redirect("user/index?keep=1");
            }
        }
    
        $this->data['message'] = $errMessage;
        $this->data['message'] .= $this->getResponseMessage($request, $validator, User::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('user.addUser', $this->data);
    }    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        
        if (!User::isExist($paramId)) {
            $request->session()->flash('message', "ไม่พบรหัสผู้ใช้ '".$paramId."'");
        }
        else if (User::deleteData($paramId)) {
            $request->session()->flash('message', "ลบผู้ใช้ '".$paramId."' แล้ว");
        }
         
        return redirect("user/index?keep=1");
        
    }
    
    function changePassword(Request $request)
    {
        $this->data['message'] = $request->session()->has("message")? $request->session()->get("message"):"";
        return $this->openView('user.changePassword', $this->data);   
    }
    
    //change password
    function changePasswordSubmit(Request $request)
    {
        if ($this->getLoginUserGroup() == Rdb::$USER_GROUP_SYSADMIN) {
            return $this->changeSysadminPasswordSubmit($request);
        }
                
        $validator = $this->genValidator($request, [
                'old_password' => 'required|max:50',
                'new_password' => 'required|max:50|min:8',
                'confirm_new_password' => 'required|same:new_password',
        ],[], $this->getFieldLabels());
        
        if (!$validator->fails()) { 
        
            $oldPassword = $request->input("old_password");
            $newPassword = $request->input("new_password");

            if (User::changePassword($this->getLoginUserId(), $oldPassword, $newPassword)) {

                $this->data['message'] = "เปลี่ยนรหัสผ่านแล้ว";
                return $this->openView('user/changePassword', $this->data); 
            }
        }
        
        $this->data['message'] = $this->getResponseMessage($request, $validator, User::getErrors(), "เกิดความผิดพลาด");
        return $this->openView('user.changePassword', $this->data);        
    }    
    
    function changeSysadminPasswordSubmit($request)
    {
        $validator = $this->genValidator($request, [
                'old_password' => 'required|max:50',
                'new_password' => 'required|max:50|min:8',
                'confirm_new_password' => 'required|same:new_password',
        ],[], $this->getFieldLabels());
    
        if (!$validator->fails()) {
    
            $oldPassword = $request->input("old_password");
            $newPassword = $request->input("new_password");
    
            if (Account::changeSysadminPassword($this->getLoginUserId(), $oldPassword, $newPassword)) {
    
                $this->data['message'] = "เปลี่ยนรหัสผ่านแล้ว";
                return $this->openView('user/changePassword', $this->data);
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, Account::getErrors(), "เกิดความผิดพลาด");
        return $this->openView('user.changePassword', $this->data);
    }    
}







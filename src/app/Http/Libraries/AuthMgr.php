<?php

namespace App\Http\Libraries;

use Log;
use App\Http\Models\User;
use App\Http\Models\Rdb;
use App\Http\Models\Account;
use App\Http\Models\UserGroupPagePermission;
use App;
use App\Http\Models\UserGroup;
use App\Http\Models\Branch;

class AuthMgr extends MyBaseLib
{        
    public $session;
    
    public function __construct() {  
        
    }
    
    public function setSession($request) {
        $this->session =  $request->session();
    }
    
    private function getSession() {
        return $this->session;
    }
    
    public function isLoggedIn() {
        
        //Log::debug("AuthMgr Logged_in");
        
        if ($this->session->has("userId")) {
            return true;
        }
        
        return false;
    }
    
    public function login($request) {
        return $this->doLogin($request->input("accountId"), $request->input("userId"), $request->input("password"));
      
    }
    
    public function doLogin($accountId, $userId, $password) {        
       
        //$accountId =  Rdb::$ACCOUNT_BASIC;
 
        if ($userId == Rdb::$ACCOUNT_SYSADMIN) { // && empty($userId)
            $accountId = Rdb::$ACCOUNT_SYSADMIN;
            
            return $this->loginAsSysadmin($accountId, $password);
        }
        
        
        if (empty($userId) || empty($password) || empty($accountId))  {
            return false;
        }
        
        
        $user = User::getData($userId, true, $accountId);

        //Log::debug("$userId / $password");
        //DataHelper::debug($user);
        
        if ($user && DataHelper::isPasswordEqual($user['password'], $password)) {
            
            //$session_data = array(
                   // 'userId'          => $user->userId,
                   // 'user_group'       => $user->user_group,
                   // 'user_group_desc'  => empty($user->user_group)?"-":$this->ci->user_group_model->get_user_group_desc($user->user_group),
            //);
             
            $this->session->put('userId', $user['userId']);
            $this->session->put('accountId', $user['accountId']);
            $this->session->put('userGroupId', $user['userGroupId']);
            $this->session->put('just_logged_in', 'yes');
            $this->session->put('app_plan_id',  '');            
    
            //$this->ci->user_model->update_last_login($userId);
            return true;
        }
    
        $this->setError('เข้าสู่ระบบไม่ได้  ข้อมูลที่ระบุไม่ถูกต้อง');
        return false;
    }
    
    public function loginAsSysadmin($accountId, $password) {
        $account = Account::getData(Rdb::$ACCOUNT_SYSADMIN);
    
        //DataHelper::debug($account);
        
        if ($account && DataHelper::isPasswordEqual($account['systemAdminPassword'], $password)) {
            $this->session->put('userId', $accountId);
            $this->session->put('accountId', $accountId);
            $this->session->put('userGroupId', Rdb::$USER_GROUP_SYSADMIN);
            $this->session->put('user_group_desc', Rdb::$USER_GROUP_SYSADMIN_DESC);
            $this->session->put('just_logged_in', 'yes');
            return true;
        }
    
        $this->setError('เข้าสู่ระบบไม่ได้  ข้อมูลที่ระบุไม่ถูกต้อง');
        return false;
    }
    
    public function setLoginBranchId($id) {
        if (empty($id)) return false;
        
        $this->session->put('branchId', $id);
        return false;
    }
    
    
    public function getLoginUserId() {
        return $this->session->get('userId');
    }
    
    public function getLoginAccountId() {
        return $this->session->get('accountId');
    }
    
    public function getLoginUserGroup() {
        return $this->session->get('userGroupId');
    }

    public function getLoginUserGroupName() {
        $id = $this->getLoginUserGroup();
        return UserGroup::getUserGroupName($id);
    }
    
    public function getLoggedInUser() {
        return $this->session->get('userId');
    }
    
    public function isUserGroupSysadmin() {
        return ( $this->getLoginUserGroup() == Rdb::$USER_GROUP_SYSADMIN);
    }
    
    public function getLoginAppPlanId() {
        return  Rdb::$APP_PLAN_ADVANCE;
        // return $this->session->get('app_plan_id');
    }    
    
    public function getLoginAppPlanIdShort() {
        $id = $this->getLoginAppPlanId();
        return "AD";
        //if ($id == Rdb::$APP_PLAN_STARTUP) return "SP";
        //if ($id == Rdb::$APP_PLAN_BASIC) return "BC";
        //if ($id == Rdb::$APP_PLAN_ADVANCE) return "AD";
        //return $id;
    }    
            
    public function getLoginBranchId() {
        return $this->session->get('branchId');
    }    
    
    public function getLoginBranchName() {
        $id = $this->getLoginBranchId();
        return Branch::getDataName($id);   
    }       
    
    
    public function logout() {
        $this->session->forget('userId');
        $this->session->forget('accountId');
        $this->session->forget('userGroupId');
        $this->session->forget('user_group_desc');        
        $this->session->forget('app_plan_id');  
        $this->session->forget('branchId');     
        return true;
    }
    
    public function checkSessionExpire() {
        //Log::debug("AuthMgr check_session_expire");

        $this->_checkSessionExpire();
        //$this->check_last_active_path();
        $this->session->put('app_last_activity', time());
    }
    
    //http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
    function _checkSessionExpire() {
        
        $expire = config("app.sessionExpire");
        //$expire = 5;
        $lastActivity = $this->session->get('app_last_activity');
    
        //log_message("error", "LAST_ACTIVITY: ".$this->ci->date_helper->time_to_sql($last_activity));
    
        //Log::debug("A:".$lastActivity. ",B:".time());
        if (empty($expire) || empty($lastActivity) || $expire <= 0) {
            return;
        }
    
        if ($lastActivity + $expire  < time()) {
            //Log::debug("EXPIRE");
            
            $this->session->forget('userId');
            $this->session->forget('app_last_activity');
            //$this->ci->session->set_userdata('app_last_active_path', $_SERVER['REQUEST_URI']);
    
            $expMiniute = DateHelper::secondsToUnit($expire);
            $this->session->flash('message', "ออกจากระบบอัตโนมัติเนื่องจากขาดการติดต่อนานกว่า $expMiniute");
            redirect('login')->send();
        }    
    }        
    
    //===============================================================================
    //
    // PART: CHECK PERMISSION
    //
    //===============================================================================
    
    
    public function hasPagePermission($pageId) {
        $userGroupId = $this->getLoginUserGroup();
        $appPlanId = $this->getLoginAppPlanId();
        
        $page = App::make('PageFactory')->getPage($pageId);
    
        //DataHelper::debug($page , "CHECK PERMISSION:");
        
        // $page_id อาจเป็น page_style/get_page_style_datatable เลยมี !page
        if (!$page ||  $this->isUserGroupSysadmin() ||  App::make('PageFactory')->isAccessiblePage($pageId) ) {
            return true;
        }
        
        if (!App::make('PageFactory')->isPageForPlan($page, $appPlanId)) {
            return false;
        }
    
        $output =  UserGroupPagePermission::hasPermission($userGroupId, $pageId); // , $appPlanId
        //DataHelper::debug("CHECK PERMISSION: $userGroupId // $pageId // $output");
        return $output;
    }
    
    public function checkPagePermission() {
        $pageId = App::make('PageMgr')->getCurrentPageId();
        
        if (!$this->hasPagePermission( $pageId )) {
            $pageName = App::make('PageFactory')->getPageName($pageId); 

            $this->session->flash('message',  "คุณไม่มีสิทธิ์ใช้ '$pageName' กรุณาติดต่อผู้ดูแลระบบ");
            redirect('home')->send();            
        }
    
        return true;
    }
    
    public function checkSystemWarning() {

        if (! function_exists('bcadd')) {
            $this->session->flash('message',  "[แจ้งเตือน] ไม่พบ library ที่ต้องติดตั้ง 'bcmath' (http://php.net/manual/en/bc.installation.php)");
            //redirect('home')->send();            
        }
    
        return true;
    }    
        
}






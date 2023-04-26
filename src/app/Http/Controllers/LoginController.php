<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Libraries\DataHelper;
use App\Http\Models\Rdb;
use App\Http\Models\SiteUsageHistory;

class LoginController extends MyBaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(false);
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //DataHelper::debug("TEST DEBUG");
        
        $data['message'] = $request->session()->has("message")? $request->session()->get("message"):"";
        return $this->openView('login', $data);
    }
    
    public function getFieldLabels() {
        return [ 'userId' => 'ผู้ใช้',  'password' => 'รหัสผ่าน', 'accountId' => 'บัญชี'];
    }
    
    public function doLogin(Request $request)
    {
        //Log::debug($request);

        $rules =  [
            //'accountId' => 'required',
            'userId' => 'required',
            'password' => 'required',
        ];
        
        if (Rdb::$ACCOUNT_SYSADMIN != $request->input('userId') ) {
             $rules['accountId'] =  'required';
        }
            
        $validator = $this->genValidator($request, $rules,[], $this->getFieldLabels());
        
        if (!$validator->fails()) {
            if ($this->authMgr->login($request)) {
                //Log::debug("LOGGEND IN");
            
                SiteUsageHistory::addData($request, "เข้าสู่ระบบ");
                
                return redirect("home");
            }
        }
        
        $this->data['message'] = $this->getResponseMessage($request, $validator, $this->authMgr->getErrors(), 'เข้าสู่ระบบไม่ได้  ข้อมูลที่ระบุไม่ถูกต้อง');
        return $this->openView('login', $this->data);
    }    
    
    public function doLogout(Request $request) {
        SiteUsageHistory::addData($request, "ออกจากระบบ");
        
        $this->authMgr->logout();
        
        return $this->openView('login');
    }
    
    public function selectBranch(Request $request) {
        $id = $request->input("data_id");
        $redirect = $request->input("redirect");
        
        //DataHelper::debug($redirectUrl);

        $redirectUrl =  (strpos($redirect, '/index') !== false)? $redirect: "home";
        $this->authMgr->setLoginBranchId($id);
        
        return redirect($redirectUrl);
    } 
    
}



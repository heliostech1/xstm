<?php

namespace App\Http\Controllers;


use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use Illuminate\Support\Facades\App;
use App\Http\Libraries\DataHelper;
use Tymon\JWTAuth\Validators\Validator;
use App\Http\Models\User;
use App\Http\Models\Rdb;

class MyBaseController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $authMgr;
    public $app;
    public $loginUser;
    public $checkAuth;
    public $checkBranch;
    
    public function __construct($checkAuth=true, $checkBranch=true) {
        $this->checkAuth = $checkAuth;
        $this->checkBranch = $checkBranch;
        
        $this->middleware(function ($request, $next) {
             $this->checkAuthOnStart($request);

             return $next($request);
        });
        
    }

    private function checkAuthOnStart($request) {
        
        $this->authMgr = App::make('AuthMgr');
        $this->authMgr->setSession($request);
                
        if (!$this->checkAuth) return;

        if (!$this->authMgr->isLoggedIn()) {
            return redirect('/login')->send();
        }

        $this->authMgr->checkSessionExpire();
        $this->authMgr->checkPagePermission();
        $this->authMgr->checkSystemWarning();    

      //  if ($this->checkBranch) {
      //      $this->checkBranchExist();
      //  }
        
    }
    
    public function checkBranchExist() {
        if ($this->isLoginAsSysadmin()) return;
        
        $id = $this->getLoginBranchId();
        if (empty($id)) {
           return redirect('/home')->send();            
        }        
    }
    
    public function openView($pageName, $data=null, $option=null) {
        return App::make('PageMgr')->view($pageName, $data, $option);
    }
    
    public function getLoginUserId() {
        return App::make('AuthMgr')->getLoginUserId();
    }
    
    public function getLoginUserGroup() {
        return App::make('AuthMgr')->getLoginUserGroup();
    }
        
    public function getLoginAccountId() {
        return App::make('AuthMgr')->getLoginAccountId();
    }
    
    public function getLoginUser() {
        if (is_null($this->loginUser)) {
            $this->loginUser = User::getData($this->getLoginUserId());
        }
        return $this->loginUser;
    }
    
    public function getLoginBranchId() {
        return App::make('AuthMgr')->getLoginBranchId();
    }
    
    
    public function getDefaultBranchId() {
        $user = $this->getLoginUser();
        return (!empty($user) && !empty($user['branchId']))? $user['branchId']:"";
    }
    
    public function getDefaultBookId() {
        $user = $this->getLoginUser();
        return (!empty($user) && !empty($user['bookId']))? $user['bookId']:"";
    }
    
    public function getLoginAppPlanId() {
        return App::make('AuthMgr')->getLoginAppPlanId();
    }        
    
    public function isStartupPlan() {
        return ($this->getLoginAppPlanId() == Rdb::$APP_PLAN_STARTUP)? true: false;
    }    
    
    public function isBasicPlan() {
        return ($this->getLoginAppPlanId() == Rdb::$APP_PLAN_BASIC)? true: false;
    }    

    public function isAdvancePlan() {
        return ($this->getLoginAppPlanId() == Rdb::$APP_PLAN_ADVANCE)? true: false;
    }    
    
    public  function isSysadminAccount() {
        return ($this->getLoginAccountId() == Rdb::$ACCOUNT_SYSADMIN )? true: false;
    }
    
    public  function isLoginAsSysadmin() {
        return ($this->getLoginAccountId() == Rdb::$ACCOUNT_SYSADMIN )? true: false;
    }
    
    /*
    public function throwMessage($request, $message) {
        $validator = Validator::make($request->all(),[]);
        $validator->getMessageBag()->add('message', 'เข้าสู่ระบบไม่ได้  ข้อมูลที่ระบุไม่ถูกต้อง');
        $this->throwValidationException($request, $validator);        
    }
    */
    
    
    //==============================================================================
    // 
    // INPUT VALIDATOR
    //
    //===============================================================================
    
    public function genValidator(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->getValidationFactory()->make($request->all(), $rules, $messages, $customAttributes);
    
        return $validator;
    }    
    
    public function getResponseMessage($request, $validator, $modelErrors = array(), $defaultMsg = "พบข้อผิดพลาด") {
        //DataHelper::debug($modelErrors, "MODEL:");
        $message = "";
        if ($validator && $validator->fails()) {
            //$errors = $this->formatValidationErrors($validator);
            $errors = $validator->errors()->getMessages();
            
            foreach ($errors as $errorArr) {
                foreach ($errorArr as $error) {
                    $message .= "<div>$error</div>";
                }
            }
        }
        else if (sizeof($modelErrors) > 0) {
            foreach ($modelErrors as $error) {
                $message .= "<div>$error</div>";
            }
        }
        else if ($request->session()->has("message")) {
            $message = $request->session()->get("message");
        }
        else {
            $message = $defaultMsg;
        }
        
        return $message;
        
    }
    
    public function addErrorToValidator($validator, $errors) {
        if (empty($errors) || sizeof($errors) <= 0) return $validator;
        
        $validator->additionErrors = $errors;
               
        $validator->after(function($validator) {
            foreach ($validator->additionErrors as $error) {
                $validator->errors()->add('', $error);
            }
        });
        
        return $validator;        
    }
    
    //===============================================================================
    //
    // STORE CRITERIA HELPER
    //
    //===============================================================================
        
    public function setCriteriaDatas($request) {
        $prefix = $this->criteriaPrefix;
        $names = $this->criteriaNames;
    
        $fieldNames = array();
        $fieldDatas = array();
    
        foreach ($names as $name) {
            $fieldName = $this->getCriteriaName($name);
            $fieldNames[$name] = $fieldName;
            $fieldDatas[$name] = $request->session()->get($fieldName);
        }
    
        //$this->data['criteriaNames'] = $names;
        $this->data['fieldNames'] = $fieldNames;
        $this->data['fieldKeys'] = $names;
        $this->data['fieldDatas'] = $fieldDatas;
        $this->data['fieldPrefix'] = $prefix;

        $this->data['tableDisplayStart'] = $request->session()->get($prefix.'_tableDisplayStart');
        $this->data['tableDisplayLength'] = $request->session()->get($prefix.'_tableDisplayLength');
        $this->data['tableSelectedId'] = $request->session()->get($prefix.'_tableSelectedId');
        $this->data['tableIsShowDetail'] = $request->session()->get($prefix.'_tableIsShowDetail');
    }
    
    public function setCriteriaDefaultData($name, $value) {
        if (!isset($_GET["keep"])) {
            if (empty($this->data['fieldDatas'][$name])) {
                $this->data['fieldDatas'][$name] = $value;
            }
        }
    }
    
    public function getFormattedCriteriaNames($prefix, $names) {
        $fieldNames = array();
    
        foreach ($names as $name) {
            $fieldName = $this->getCriteriaName($name, $prefix);
            $fieldNames[$name] = $fieldName;
        }
        return $fieldNames;
    }
    
    public function getCriteriaDatas($request) {
        $names = $this->criteriaNames;
    
        $fieldDatas = array();
    
        foreach ($names as $name) {
            $fieldName = $this->getCriteriaName($name);            
            
            $fieldDatas[$name] =  (isset($_POST[$fieldName]) && $_POST[$fieldName] != "undefined") ?
               $request->input($fieldName) : "";
        }
    
        // varDump($fieldDatas);
        return $fieldDatas;
    }
    
    public function getCriteriaData($request, $name) {
        $fieldName = $this->getCriteriaName($name);

        $data =  (isset($_POST[$fieldName]) && $_POST[$fieldName] != "undefined") ?
            $request->input($fieldName) : "";
        return $data;
    }
    
    public function getCriteriaName($name, $prefix=null) {
        $prefix = (empty($prefix))? $this->criteriaPrefix: $prefix;
    
        return  $prefix."_criteria_".$name;
    }
    
    public function cacheCriteriaDatas($request) {
    
        $prefix = $this->criteriaPrefix;
        $names = $this->criteriaNames;
    
        foreach ($names as $name) {
            $fieldName =  $this->getCriteriaName($name);
            $request->session()->put($fieldName, $request->input($fieldName));
        }
    
        $request->session()->put($prefix.'_tableDisplayStart', $request->input($prefix.'_tableDisplayStart'));
        $request->session()->put($prefix.'_tableDisplayLength', $request->input($prefix.'_tableDisplayLength'));
        $request->session()->put($prefix.'_tableSelectedId', $request->input($prefix.'_tableSelectedId'));
        $request->session()->put($prefix.'_tableIsShowDetail', $request->input($prefix.'_tableIsShowDetail'));
    
    }
    
    public function clearCacheCriteriaDatas($request) {
    
        $prefix = $this->criteriaPrefix;
        $names = $this->criteriaNames;
    
        foreach ($names as $name) {
            $fieldName = $this->getCriteriaName($name);
            $request->session()->forget($fieldName);
        }
    
        $request->session()->forget($prefix.'_tableDisplayStart');
        $request->session()->forget($prefix.'_tableDisplayLength');
        $request->session()->forget($prefix.'_tableSelectedId');
        $request->session()->forget($prefix.'_tableIsShowDetail');
    }    
}





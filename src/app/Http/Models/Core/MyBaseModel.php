<?php

namespace App\Http\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Libraries\DataHelper;
use App;
use App\Http\Libraries\FormatHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;

class MyBaseModel extends Model
{

    public static function getLoginUserId() {
        if (self::isCommandLine()) return "system";
        
        return App::make('AuthMgr')->getLoginUserId();
    }
    
    public static function getLoginAccountId() {
        if (self::isCommandLine() && !empty( config("cmdAccountId"))  ) return config("cmdAccountId");
        
        return App::make('AuthMgr')->getLoginAccountId();
    }
    
    public static function getLoginAppPlanId() {
        if (self::isCommandLine() && !empty( config("cmdAppPlanId"))  ) return config("cmdAppPlanId");

        return App::make('AuthMgr')->getLoginAppPlanId();
    }        
    
    public static function getLoginBranchId() {
        return App::make('AuthMgr')->getLoginBranchId();
    }       
    
    public static function isStartupPlan() {
        return (self::getLoginAppPlanId() == Rdb::$APP_PLAN_STARTUP)? true: false;
    }    
    
    public static function isBasicPlan() {
        return (self::getLoginAppPlanId() == Rdb::$APP_PLAN_BASIC)? true: false;
    }    

    public static function isAdvancePlan() {
        return (self::getLoginAppPlanId() == Rdb::$APP_PLAN_ADVANCE)? true: false;
    }    
    
    public static function isSysadminAccount() {
        if (self::getLoginAccountId() == Rdb::$ACCOUNT_SYSADMIN ) {
           return true;   
        }
        return false;
    }
   
    public static function isCommandLine() {
        if (php_sapi_name() == "cli") {
            return true;
        } else {
            return false;
        }
    }    


    //===============================================================================
    //
    // PART: VALIDATE
    //
    //===============================================================================
    
    public static function checkValidThaiDate($date_str, $label) {
        
        if (!empty($date_str) && !DateHelper::isValidThaiDate($date_str)) {
            self::setError( "ช่อง '$label' ระบุข้อมูลวันที่ไม่ถูกต้อง");
            return false;
        }
    
        return true;
    }    
    
    //===============================================================================
    //
    // PART: ERROR
    //
    //===============================================================================

    protected static $errors = array();
    
    public static function setError($error) {
        if (is_array($error)) {
            self::$errors = array_merge(self::$errors, $error);
        }
        else {
            self::$errors[] = $error;
        }
         
        //DataHelper::debug(self::$errors, "SET ERROR: $error");
        return $error;
    }
    
    public static function hasErrors() {
        if (sizeof(self::$errors) > 0) {
            return true;
        }
        false;
    }
    
    public static function getErrors() {
        $errors = self::$errors;
        static::clearErrors();
        return $errors;
    }   
    
    public static function clearErrors() {
        self::$errors = array();
    }
    
    public static function errors() {
        $output = '';
        foreach (self::$errors as $error) {
            $output .= FormatHelper::wrapInfoDiv($error);
        }
    
        static::clearErrors();
        return $output;
    }    
    
    public static function errorsPlainText() {
        $output = implode(",", self::$errors);
        static::clearErrors();
        return $output;
    }    
     
}


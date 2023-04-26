<?php

namespace App\Http\Models;

use App\Http\Models\Core\MyBaseModel;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DataHelper;

class Rdb extends MyBaseModel
{
        
    public static $APP_PLAN_STARTUP = "startup"; 
    public static $APP_PLAN_BASIC = "basic"; 
    public static $APP_PLAN_ADVANCE = "advance";
    
    
    public static $ACCOUNT_SYSADMIN = "sysadmin";
    public static $ACCOUNT_BASIC = "basic";
    
    public static $ACCOUNT_TYPE_SYSADMIN = "sysadmin";
    public static $ACCOUNT_TYPE_CUSTOMER = "customer";
    
    public static $ALL_BRANCH = "ALL_BRANCH";
    
    public static $USER_GROUP_ADMIN = "admin";     
    public static $USER_GROUP_SYSADMIN = "system_admin";
    public static $USER_GROUP_SYSADMIN_DESC = "System Administrator";
    
    public static $DEFAULT_UNIT_CODE = "999";
    
    public static $YES = "Y";
    public static $NO = "N";
    
    public static $THUMB_DIR_NAME = "__thumb";
    
    public static $NO_BRANCH = "no-branch";
    public static $STARTUP_BRANCH = "startup-branch";
    
    public static $OTHER_OPTION = "other_option";    
  
          
    public static $APP_SETTING_IMPORT_INTERVAL = "import_interval";     
    public static $APP_SETTING_API_TOKEN = "api_token";

    public static $APP_SETTING_LPG_TANK_LIFE_YEAR = "lpg_tank_life_year";
    public static $APP_SETTING_CNG_TANK_LIFE_YEAR = "cng_tank_life_year";
    public static $APP_SETTING_ALARM_DEBUG_MODE = "alarm_debug_mode";    
            
    public static $MESSAGE_STATUS_SENT = "sent";
    public static $MESSAGE_STATUS_DELETED = "deleted";    
    public static $MESSAGE_STATUS_READ = "read";   
    
    //-------------------------------------------------------
    public static $HTTP_CODE_OK = "200";
    public static $HTTP_CODE_AUTH_FAIL = "401";
    public static $HTTP_CODE_ERROR = "400";
    
  
    public static $CHAT_OWNER_TYPE_STAFF = "staff";
    public static $CHAT_OWNER_TYPE_USER = "user";

    //---------------------------------------------------------------

    public static $BRANCH_HEADQUARTER_CODE = "000"; 
    public static $BRANCH_HEADQUARTER_NAME = "สำนักงานใหญ่";

    
    public static $VEHICLE_POSITION_HEAD = "หัวรถ";
    public static $VEHICLE_POSITION_TAIL = "หางรถ";
   // public static $VEHICLE_POSITION_ONLY_HEAD = "หัวรถไม่มีหาง";
            
    
    public static $USER_MANUAL_KEY = "main";


    public static $FUEL_BENSIN = "bensin";
    public static $FUEL_DISEL = "disel";
    public static $FUEL_LPG = "lpg";
    public static $FUEL_CNG = "cng";    
    
    
    public static $CLAIM_TYPE_ACT = "พรบ";
    public static $CLAIM_TYPE_INS_CAR = "ประกันภัยรถยนต์";
    public static $CLAIM_TYPE_INS_GOODS =  "ประกันภัยสินค้า";
    
    public static $MONITOR_DATATYPE_ODO = "odometer";
    public static $MONITOR_DATATYPE_DATE = "date";    
    
    public static $REPAIR_GROUP_PM = "pm";
    public static $REPAIR_GROUP_CM = "cm";
    
    public static $ALARM_SETTING_VEHICLE_MONITOR = "vehicle_monitor";  
    
    public static $ALARM_STATUS_DONE = "แจ้งเตือนแล้ว";  
    public static $ALARM_STATUS_CHECKING = "ไม่ถึงกำหนด";  
    public static $ALARM_STATUS_NO_SETTING = "";  
    
    public static $ALARM_TYPE_WARN = "Warning";  
    public static $ALARM_TYPE_ALERT = "Alert";  
    
    
    static function getActive($name=false) {
        $datas = array(
                array( "name" => self::$YES, "description" => "ใช้งานได้"),
                array( "name" => self::$NO, "description" => "ระงับการใช้"),
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    
    static function getYesNo($name=false, $yes="ใช่", $no="ไม่ใช่") {
        $datas = array(
                array( "name" => self::$YES, "description" => $yes),
                array( "name" => self::$NO, "description" => $no),
        );
        
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    
    static function getAppPlan($name=false) {

        $datas = array(
                array( "name" => self::$APP_PLAN_STARTUP, "description" => "Startup"),
                array( "name" => self::$APP_PLAN_BASIC, "description" => "Basic"),
                array( "name" => self::$APP_PLAN_ADVANCE, "description" => "Advance"), 
        );
        
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }

    
    static function getMessageStatus($name=false) {
        $datas = array(
                array( "name" => self::$MESSAGE_STATUS_SENT, "description" => "ส่งแล้ว"),
                array( "name" => self::$MESSAGE_STATUS_READ, "description" => "อ่านแล้ว"),
                array( "name" => self::$MESSAGE_STATUS_DELETED, "description" => "ถูกลบ"),            
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    

    
    static function getCategoryType($name=false) {
        $datas = array(
                array( "name" => self::$CATEGORY_TYPE_DRUG, "description" => "ยา"), 
        );
        
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }    
    
    

    static function getAppSetting($id=false) {
        $datas = array(
            array( "category" => "API",  "settingId" => self::$APP_SETTING_API_TOKEN, "name" => "API TOKEN", "defaultValue" => "token@abc1234company", "unit" => ""),
            array( "category" => "งานแจ้งเตือน",  "settingId" => self::$APP_SETTING_ALARM_DEBUG_MODE, "name" => "เปิดใช้โหมด debug สำหรับงานแจ้งเตือน ( 1 = เปิด )", "defaultValue" => "0", "unit" => ""),
  
        );
    
        return ($id === false)? $datas: DataHelper::findInArray($datas, "settingId", $id);        
    }
    
    static function getAppSettingForUser($id=false) {
        $datas = array(
            array( "category" => "รถขนส่ง",  "settingId" => self::$APP_SETTING_LPG_TANK_LIFE_YEAR, "name" => "อายุการใช้งานถัง LPG (ปี)", "defaultValue" => "0", "unit" => "ปี"),
            array( "category" => "รถขนส่ง",  "settingId" => self::$APP_SETTING_CNG_TANK_LIFE_YEAR, "name" => "อายุการใช้งานถัง CNG (ปี)", "defaultValue" => "0", "unit" => "ปี"),

        );
   
        return ($id === false)? $datas: DataHelper::findInArray($datas, "settingId", $id);        
    }
    
    
    static function getVehiclePosition($name=false) {
        $datas = array(
                array( "name" => self::$VEHICLE_POSITION_HEAD, "description" => self::$VEHICLE_POSITION_HEAD), 
                array( "name" => self::$VEHICLE_POSITION_TAIL, "description" => self::$VEHICLE_POSITION_TAIL),   
              //  array( "name" => self::$VEHICLE_POSITION_ONLY_HEAD, "description" => self::$VEHICLE_POSITION_ONLY_HEAD),             
        );
        
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }    
    
  
    
   static function getFuel($name=false) {
        $datas = array(
                array( "name" => self::$FUEL_BENSIN, "description" => "เบนซิน"),
                array( "name" => self::$FUEL_DISEL, "description" => "ดีเซล"),
                array( "name" => self::$FUEL_LPG, "description" => "LPG"),
                array( "name" => self::$FUEL_CNG, "description" => "CNG"),       
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    
   static function getFuelOil($name=false) {
        $datas = array(
                array( "name" => self::$FUEL_BENSIN, "description" => "เบนซิน"),
                array( "name" => self::$FUEL_DISEL, "description" => "ดีเซล"),    
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    
   static function getFuelGas($name=false) {
        $datas = array(
                array( "name" => self::$FUEL_LPG, "description" => "LPG"),
                array( "name" => self::$FUEL_CNG, "description" => "CNG"),       
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    
   static function getClaimType($name=false) {
        $datas = array(
                array( "name" => self::$CLAIM_TYPE_ACT, "description" =>  self::$CLAIM_TYPE_ACT ),
                array( "name" => self::$CLAIM_TYPE_INS_CAR, "description" =>  self::$CLAIM_TYPE_INS_CAR ),       
                array( "name" => self::$CLAIM_TYPE_INS_GOODS, "description" => self::$CLAIM_TYPE_INS_GOODS ),               
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    
    static function getWorkStatus($name=false) {
        $datas = array(
                array( "name" => self::$YES, "description" => "ทำงานอยู่"),
                array( "name" => self::$NO, "description" => "พันสภาพพนักงาน"),       
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }

    static function getMonitorDataType($name=false) {
        $datas = array(
                array( "name" => self::$MONITOR_DATATYPE_ODO, "description" => "เลขไมล์"),
                array( "name" => self::$MONITOR_DATATYPE_DATE, "description" => "ระยะเวลา(วัน)"),       
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    
    //======================================================================
    
    
    static function getMonth($name=false) {
        $datas = array(
                array( "name" => "1", "description" => "มกราคม"),
                array( "name" => "2", "description" => "กุมภาพันธ์"),
                array( "name" => "3", "description" => "มีนาคม"),
                array( "name" => "4", "description" => "เมษายน"),
                array( "name" => "5", "description" => "พฤษภาคม"),
                array( "name" => "6", "description" => "มิถุนายน"),
                array( "name" => "7", "description" => "กรกฎาคม"),
                array( "name" => "8", "description" => "สิงหาคม"),
                array( "name" => "9", "description" => "กันยายน"),
                array( "name" => "10", "description" => "ตุลาคม"),
                array( "name" => "11", "description" => "พฤศจิกายน"),
                array( "name" => "12", "description" => "ธันวาคม"),          
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }

    
    static function getLineMessageType($name=false) {
        $datas = array(
                array( "name" => self::$LINE_MESSAGE_TYPE_TEXT, "description" => "ข้อความ"),
                array( "name" => self::$LINE_MESSAGE_TYPE_IMAGE, "description" => "รูปภาพ"),
                array( "name" => self::$LINE_MESSAGE_TYPE_FILE, "description" => "ไฟล์"),            
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    

    static function getRepairGroup($name=false) {
        $datas = array(
                array( "name" => self::$REPAIR_GROUP_PM, "description" => "Preventive maintenance (PM)"),
                array( "name" => self::$REPAIR_GROUP_CM, "description" => "Corrective maintenance (CM)"),
            
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }
    
    
    static function getAlarmSetting($name=false) {
        $datas = array(
                array( "name" => self::$ALARM_SETTING_VEHICLE_MONITOR , "description" => "แจ้งเตือนงานซ่อมบำรุงรถ"),                   
        );
    
        return ($name === false)? $datas: self::findDesc($datas, $name);
    }   
    
    
    
    public static function getUpdateSuccess() {
        return "แก้ไขข้อมูลแล้ว";
    }
        
    public static function getDeleteSuccess($name) {
        return "ลบข้อมูล '".$name."' แล้ว";
    }
    
    
    
    //========================================================
    
    static function findDesc($result_array, $name) {
        if (empty($name)) return "";
        
        return self::findProp($result_array, "description", array('name', $name));
    }
    
    static function findCode($result_array, $name) {
        return self::findProp($result_array, "code", array('name', $name));
    }
    
    static function findProp($result_array, $prop , $where) {
        if (!empty($where[1]) || $where[1] === 0 || $where[1] === '0') {
            foreach ($result_array as $row) {
                if ($row[$where[0]] == $where[1]) {
                    return $row[$prop];
                }
            }
        }
        return $where[1];
    }
     
    
    static function findPropByMongoId($resultArray, $prop , $mongoId) {
        if (!empty($mongoId) || $mongoId === 0 || $mongoId === '0') {
            foreach ($resultArray as $row) {
                $id = MongoHelper::getIdByObject($row['_id']);                
                if ($id == $mongoId) {
                    return $row[$prop];
                }
            }
        }
        return $mongoId;
    }    
}



<?php

namespace App\Http\Libraries\Alarm;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\MyBaseLib;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;
use App\Http\Models\Alarm\AlarmSetting;
use App\Http\Models\Alarm\AlarmLog;
use App\Http\Libraries\Alarm\AlarmVehMonitorHelper;

use Illuminate\Support\Facades\Config;
use App\Http\Models\AppSetting\AppSetting;

class AlarmSender extends MyBaseLib
{
    public static function start($nowSql = null) {
        if (empty($nowSql)){
            $nowSql = DateHelper::nowSql();
        }
        
        self::debug("ALARM VEHICLE ---------- START --------- ($nowSql)");
     
        AlarmVehMonitorHelper::startCheck($nowSql);
   
        
        self::debug("ALARM ---------- FINISH");   
        
    }   
    
    public static function startTest() {
        
        self::debug("ALARM VEHICLE ---------- START TEST --------- ");
     
        AlarmVehMonitorHelper::startTest();

        self::debug("ALARM ---------- FINISH");   
        
    }   
    
    //=========================================================================
    //
    // UTILITY
    //
    //=========================================================================
    
    private static function validateParamDate($data) {
        if (empty($data)) {
            return true;
        }
        
        if (!DateHelper::isValidSqlDate($data)) {
            self::debug("WRONG FORMAT PARAMETER DATE !!! ( date format is YYYY-MM-DD ex. 2021-02-23 ) ");
            return false;
        }
        return true;
    }
    
    public static function debug($message) {   
        if (! AppSetting::isAlarmDebugMode() ) return;
        
        if (is_array($message)) {
            echo "\n";
            echo print_r($message);
        }
        else {
            echo "\n".$message;
        }
        
        DataHelper::debug($message);
    }
    
    public static function logError($message) {  
        echo "\n".$message;
        DataHelper::debug($message);
    }
    
}
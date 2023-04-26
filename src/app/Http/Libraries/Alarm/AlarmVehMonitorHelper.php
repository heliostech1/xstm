<?php

namespace App\Http\Libraries\Alarm;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\MyBaseLib;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;
use App\Http\Models\Alarm\AlarmSetting;
use App\Http\Models\Alarm\AlarmLog;
use App\Http\Libraries\Alarm\AlarmSender;
use App\Http\Models\Vehicle\Vehicle;
use App\Http\Libraries\BigPage\VehMonitorHelper;
use App\Http\Models\Alarm\MonitorPlan;
use App\Http\Libraries\MongoHelper;

class AlarmVehMonitorHelper extends MyBaseLib
{
    
    public static function startCheck($nowSql) {

        $setting = AlarmSetting::getData(Rdb::$ALARM_SETTING_VEHICLE_MONITOR);
        
        if (getMyProp($setting, "enable") != Rdb::$YES) {
            AlarmSender::debug("alarm is not enable");
            return false;            
        }
        
        
        //======================================================== CHECK TIME
        
        $timeListForCheckDate = DataHelper::stringToArray( getMyProp($setting, 'alarmTimeForCheckDate') );
        $timeListForCheckOdo = DataHelper::stringToArray( getMyProp($setting, 'alarmTimeForCheckOdo') );
        
        $currentTime = date("H:i", strtotime($nowSql) );
    
        if (!in_array($currentTime, $timeListForCheckDate)) {
            AlarmSender::debug("present is not alarm time (check date)");
           // return false;   
        }
        else {
            AlarmSender::debug("start (check date)");
            self::startCheckForType(Rdb::$MONITOR_DATATYPE_DATE);
        }
        
        
        if (!in_array($currentTime, $timeListForCheckOdo)) {
            AlarmSender::debug("present is not alarm time (check mileage)");
           // return false;   
        }
        else {
            AlarmSender::debug("start (check mileage)");
            self::startCheckForType(Rdb::$MONITOR_DATATYPE_ODO);
        }
        
        return true;
                
    }
    
    
    public static function startTest() {
        self::startCheckForType(Rdb::$MONITOR_DATATYPE_DATE);
        self::startCheckForType(Rdb::$MONITOR_DATATYPE_ODO);
    }   
    
    
    public static function startCheckForType($checkType) {
        
        $vehicles = Vehicle::getAllDataInSystem();
        $monitorPlans  = MonitorPlan::getAllDataInSystem();
        //$alarmDatas = array();       
                
        foreach ($vehicles as $vehicle) {
            self::startCheckForVehicle($checkType, $vehicle, $monitorPlans);
        }
       
        return true;
    }
    
    public static function startCheckForVehicle($checkType, $vehicle, $monitorPlans) {
        
        $partMonitor = getMyProp($vehicle, "partMonitor_");
        $monitorDatas = getMyProp($partMonitor, "monitorDatas");
        $monitorPlanId = getMyProp($partMonitor, "monitorPlan");

        $monitorPlan = self::getMonitorPlanFromAll($monitorPlanId, $monitorPlans);
        $planDetails = getMyProp($monitorPlan, "detailDatas", array());

        foreach ($planDetails as $planDetail) {
            $dataType = getMyProp($planDetail, 'dataType', '');
              
            if ($dataType == $checkType) {
                self::startCheckForTopic($vehicle, $monitorDatas, $planDetail ); 
            }   
        }

        return true;
    }    

    public static function startCheckForTopic($vehicle, $monitorDatas, $planDetail ) {


        $currentDate = DateHelper::todaySql();       
        $currentOdo = DataHelper::toInteger( getMyProp($vehicle, "odometer") );
        $vehicleId =  getMyProp($vehicle, "vehicleId");
        
        $monitorTopic = getMyProp($planDetail, 'monitorTopic', '');
        $dataType = getMyProp($planDetail, 'dataType', '');
           
        $warnAmount = getMyProp($planDetail, 'warnAmount', '');
        $alertAmount = getMyProp($planDetail, 'alertAmount', '');

        $monitorData = self::getMonitorDataByTopic($monitorTopic, $monitorDatas);

        $warnAtByUser = VehMonitorHelper::getAlarmValueByUser( $vehicleId, $monitorData, Rdb::$ALARM_TYPE_WARN, getMyProp($monitorData, 'warnAt') ); 
        $alertAtByUser = VehMonitorHelper::getAlarmValueByUser( $vehicleId, $monitorData, Rdb::$ALARM_TYPE_ALERT, getMyProp($monitorData, 'alertAt') ); 

        $warnAt = VehMonitorHelper::calAlarmValueByDataType($vehicle, $warnAtByUser, $dataType, $warnAmount, $monitorData);
        $alertAt = VehMonitorHelper::calAlarmValueByDataType($vehicle, $alertAtByUser, $dataType, $alertAmount, $monitorData);

     
        if ($dataType == Rdb::$MONITOR_DATATYPE_DATE) {
            $warnAtSql = DateHelper::thaiToSql($warnAt);
            $alertAtSql = DateHelper::thaiToSql($alertAt);

            if (self::isValidSqlDate($warnAtSql) && $currentDate >= $warnAtSql   ) {
                $message = "วันที่ถึงกำหนด $warnAt";
                self::addAlarmData($vehicle, $monitorData, $dataType, Rdb::$ALARM_TYPE_WARN, $message, $warnAt);
            }
            
            if (self::isValidSqlDate($alertAtSql) && $currentDate >= $alertAtSql  ) {
                $message = "วันที่ถึงกำหนด $alertAt";
                self::addAlarmData($vehicle, $monitorData, $dataType, Rdb::$ALARM_TYPE_ALERT, $message, $alertAt);
            }
            
        } 
        else if ($dataType == Rdb::$MONITOR_DATATYPE_ODO && !empty($currentOdo)) {
            $warnAtOdo = DataHelper::toInteger($warnAt, 0);
            $alertAtOdo = DataHelper::toInteger($alertAt, 0);

            
            if ( $currentOdo >= $warnAtOdo  ) {
                $message = "เลขไมล์ถึงกำหนด $warnAtOdo";
                self::addAlarmData($vehicle, $monitorData, $dataType, Rdb::$ALARM_TYPE_WARN, $message, $warnAt);
            }
            
            if ( $currentOdo >= $alertAtOdo ) {
                $message = "เลขไมล์ถึงกำหนด $alertAtOdo";
                self::addAlarmData($vehicle, $monitorData, $dataType, Rdb::$ALARM_TYPE_ALERT, $message, $alertAt);
            }
        }
        
        return true;
    }
    
    private static function addAlarmData($vehicle, $monitorData, $checkType, $alarmType, $message, $destValue) {
        
        $monitorTopic = getMyProp($monitorData, 'monitorTopic', '');
        $lastRepairDate = getMyProp($monitorData, 'lastRepairDate', ''); // mongodate => thai มาก่อนแล้ว
        $lastRepairOdo = getMyProp($monitorData, 'lastRepairOdo', '');
            
        $vehicleId = getMyProp($vehicle, "vehicleId");
        $licensePlate = getMyProp($vehicle, "licensePlate");
        $itemKey = AlarmLog::formatItemKey($vehicleId, $monitorTopic, $alarmType, $destValue);
                
        
        if ( AlarmLog::isExistItemKey($itemKey)) {
           return;
        }
        
        AlarmSender::debug("alarm data created: $licensePlate, $monitorTopic, $checkType, $alarmType ");
        
        $updateData = array(
            "accountId" => getMyProp($vehicle, "accountId"),
            "vehicleId" => $vehicleId,
            "licensePlate" => $licensePlate,
            "odometer" => getMyProp($vehicle, "odometer"),
            
            "monitorTopic" => $monitorTopic,
            "checkType" => $checkType,
            "alarmType" => $alarmType,
            "message" => $message,
            "alarmDate" =>  MongoHelper::date(),
            
            "itemKey" => $itemKey,
            "lastRepairDate" =>  $lastRepairDate,  
            "lastRepairOdo" => $lastRepairOdo,   
            "destValue" => $destValue, 
        );
        
        AlarmLog::addData($updateData);
        
        return true;
    }
    

    
    private static function isValidSqlDate($data) {
        if (empty($data)) return false;
        
        if ( DateHelper::isValidSqlDate($data) ) {
            return true;
        }
        return false;
    }
    
    private static function getMonitorDataByTopic($monitorTopic, $datas) {

        foreach ($datas as $data) {
            if ( getMyProp($data, 'monitorTopic') == $monitorTopic) {
                return $data;
            }
        }

        return null;
    }
    
    private static function getMonitorPlanFromAll( $monitorPlanId, $monitorPlans) {    
        if (empty($monitorPlanId) || empty($monitorPlans)) return false;
        
        foreach ($monitorPlans as $monitorPlan) {
            $mongoId = MongoHelper::getIdByObject($monitorPlan['_id']);
            
            if ($monitorPlanId == $mongoId) {
                return $monitorPlan;
            }
        }
        return false;
    }


    
}
<?php

namespace App\Http\Libraries\BigPage;

use Closure;
use Log;
use DB;
use App\Http\Models\Rdb;
use App\Http\Models\AppSetting\AppSettingForUser;
use App\Http\Libraries\DateHelper;
use App\Http\Libraries\BigPage\VehicleHelper;
use App\Http\Models\Alarm\MonitorPlan;
use App\Http\Libraries\MyBaseLib;
use App\Http\Libraries\DataHelper;
use App\Http\Models\Vehicle\Vehicle;
use App\Http\Models\Alarm\AlarmLog;

class VehMonitorHelper extends MyBaseLib
{          

    //-------------------------------------------------------------------------
    // 1. ACT พรบ
    
   
        
    public static function  getMonitorDatasForView($result) {
        $vehicleId = getMyProp($result, "vehicleId", "");
        $partMonitor = getMyProp($result, "partMonitor_", "");
        $monitorDatas = getMyProp($partMonitor, "monitorDatas");
        $monitorPlanId = getMyProp($partMonitor, "monitorPlan");
        
        $monitorPlan = MonitorPlan::getData($monitorPlanId);
        $planDetails = getMyProp($monitorPlan, "detailDatas", array());
        
        $dataRows = array();
        
        foreach ($planDetails as $planDetail) {
            $monitorTopic = getMyProp($planDetail, 'monitorTopic', '');
            $dataType = getMyProp($planDetail, 'dataType', '');
            $warnAmount = getMyProp($planDetail, 'warnAmount', '');
            $alertAmount =  getMyProp($planDetail, 'alertAmount', '');
            
            $monitorData = self::getMonitorDataByTopic( $monitorTopic, $monitorDatas);
            
            $lastRepairDate = DateHelper::mongoDateToThai( getMyProp($monitorData, 'lastRepairDate', ''), false); 
            $lastRepairOdo = getMyProp($monitorData, 'lastRepairOdo', '');
            
            $warnAtByUser = self::getAlarmValueByUser( $vehicleId, $monitorData, Rdb::$ALARM_TYPE_WARN, getMyProp($monitorData, 'warnAt') ); 
            $alertAtByUser = self::getAlarmValueByUser( $vehicleId, $monitorData, Rdb::$ALARM_TYPE_ALERT, getMyProp($monitorData, 'alertAt') ); 
            
            
            $warnAt = self::calAlarmValueByDataType($result, $warnAtByUser, $dataType, $warnAmount,  $monitorData );
            $alertAt = self::calAlarmValueByDataType($result, $alertAtByUser, $dataType, $alertAmount, $monitorData );
            
            $hasWarn = AlarmLog::isExistItemKeyByCri($vehicleId, $monitorTopic, Rdb::$ALARM_TYPE_WARN, $warnAt);
            $hasAlert = AlarmLog::isExistItemKeyByCri($vehicleId, $monitorTopic, Rdb::$ALARM_TYPE_ALERT, $alertAt);
            
            $dataRows[] = array(
                    "monitorTopic" => $monitorTopic,   
                    "itemName" => getMyProp($planDetail, 'itemName', ''),    
                    "dataTypeDesc" => getMyProp($planDetail, 'dataTypeDesc', ''), 
                    "warnAmount" =>  $warnAmount, 
                    "alertAmount" =>  $alertAmount,   
                
                    "lastRepairDate" => $lastRepairDate, // DateHelper::mongoDateToThai($lastRepairDate, false),
                    "lastRepairOdo" => $lastRepairOdo,
                
                    "warnAt" => $warnAt,
                    "warnStatus" => ($hasWarn)? "แจ้งเตือนแล้ว": "",
                
                    "alertAt" => $alertAt,
                    "alertStatus" => ($hasAlert)? "แจ้งเตือนแล้ว": "",
            );
            
        }
        
                
        $output = array();
        $output["partMonitorView_monitorDatas"] = $dataRows;
        return $output;
    }

    public static function getAlarmValueByUser($vehicleId, $monitorData, $alarmType, $dataByUser) {        
        $monitorTopic = getMyProp($monitorData, 'monitorTopic', '');
        $lastRepairDate = getMyProp($monitorData, 'lastRepairDate', '');
        
        if (empty($lastRepairDate)) {
            return $dataByUser;
        }
        
        $hasAlarmData  = AlarmLog::isExistItemKeyByCri($vehicleId, $monitorTopic, $alarmType, $dataByUser);
        if ($hasAlarmData) {
            return "";
        }
        return $dataByUser;            
    }
    
    
    public static function calAlarmValueByDataType($vehicle, $dataByUser, $dataType, $amount, $monitorData) {
        if (!empty($dataByUser)) return $dataByUser;
        
        $lastRepairDate = DateHelper::mongoDateToThai( getMyProp($monitorData, 'lastRepairDate', ''), false); 
        $lastRepairOdo = getMyProp($monitorData, 'lastRepairOdo', '');           
        
        
        $amount = DataHelper::toInteger($amount);
        if (empty($amount)) return "";
        
        if ($dataType == Rdb::$MONITOR_DATATYPE_DATE) {
            if (!empty($lastRepairDate)) {
                return DateHelper::getBesideThaiDate($lastRepairDate, $amount) ;
            }
            
        }
        else if ($dataType == Rdb::$MONITOR_DATATYPE_ODO ) {
            if (!empty($lastRepairOdo)) {
                return DataHelper::toInteger($lastRepairOdo) +  $amount;
            }
            
        }
        
        return "";
        
    }
    
    
    private static function getMonitorDataByTopic($monitorTopic, $datas) {

        foreach ($datas as $data) {
            if ( getMyProp($data, 'monitorTopic') == $monitorTopic) {
                return $data;
            }
        }

        return null;
    }
    
    //==========================================================
    
    public static function  updateVehicleLastRepair($vehicleId, $repairDatas) {
        $vehicle = Vehicle::getDataByVehicleId($vehicleId);
        
        $partMonitor = getMyProp($vehicle, "partMonitor_", array());
        $monitorDatas = getMyProp($partMonitor, "monitorDatas", array()); 
        $newDatas = array();
        
        foreach ($monitorDatas as $monitorData) {
            $newData = $monitorData;
            $monitorTopic  = getMyProp($monitorData, 'monitorTopic');
            $repairData = self::getLastRepairByTopic($monitorTopic, $repairDatas);
            
           // if (!empty($repairData)) {
                $newData['lastRepairDate'] = getMyProp($repairData, 'fixEndDate' , null); // mongodate
                $newData['lastRepairOdo'] = getMyProp($repairData, 'odometer', '');
           // }
            
            $newDatas[]= $newData;
        }

        $updateData = array(
            "partMonitor_.monitorDatas" => $newDatas
        );
        
        //myDebug($vehicleId);
        //myDebug($updateData);
        
        Vehicle::editSimpleDataByVehicleId($vehicleId, $updateData );
        return true;
        
    }
    
    public static function  getLastRepairByTopic($monitorTopic, $repairDatas) {
        if (empty($monitorTopic) || empty($repairDatas)) return null;
        
        $maxDate = null;
        $output = null;
        
        foreach ($repairDatas as $repairData) {
           $fixEndDate = getMyProp($repairData, 'fixEndDate');
           $monitorTopicDatas = DataHelper::stringToArray( getMyProp($repairData, 'monitorTopicDatas') );
           
           if (in_array($monitorTopic, $monitorTopicDatas)) {
               if (empty($maxDate) || $maxDate <= $fixEndDate) {
                   $output = $repairData;
                   $maxDate = $fixEndDate;
               }
           }
        } 
        
        return $output;
    }    
    
    //=================================================================
    
    
    public static function mergePartMonitorWithOldData( $newPartMonitor, $oldData ) {
        
        $newMonitorDatas = getMyProp($newPartMonitor, "monitorDatas", array()); 
        $partMonitor = getMyProp($oldData, "partMonitor_", array()); 
        $oldMonitorDatas = getMyProp($partMonitor, "monitorDatas", array()); 
        
        $newDatas = array();
        
        foreach ($newMonitorDatas as $newMonitorData) {
            $newData = $newMonitorData;
            $monitorTopic  = getMyProp($newMonitorData, 'monitorTopic');
            $oldData = self::getOldDataByTopic($monitorTopic, $oldMonitorDatas);

            $newData['lastRepairDate'] = getMyProp($oldData, 'lastRepairDate' , null); // mongodate
            $newData['lastRepairOdo'] = getMyProp($oldData, 'lastRepairOdo', '');
            $newData['warnStatus'] =  getMyProp($oldData, 'warnStatus', '');
            $newData['alarmStatus'] =  getMyProp($oldData, 'alarmStatus', '');
            $newDatas[]= $newData;
        }
        
        $newPartMonitor["monitorDatas"] = $newDatas;
        
        //myDebug($newPartMonitor);
        return $newPartMonitor;
    }
        
        
    private static function getOldDataByTopic( $monitorTopic, $oldMonitorDatas ) {
        if (empty($oldMonitorDatas)) return null;
        
        foreach ($oldMonitorDatas as $data) {
            if (getMyProp($data, "monitorTopic") == $monitorTopic) {
                return $data;
            }
        }
        return null;
        
    }
    
    
}
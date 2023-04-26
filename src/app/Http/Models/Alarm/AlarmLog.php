<?php

namespace App\Http\Models\Alarm;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DropdownMgr;


class AlarmLog extends MyBaseModel
{    
    static protected $TABLE_NAME = 'alarm_log';
    
    protected $primaryKey = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDatatable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        $error = "";        
            
        $where = MongoHelper::appendWhere($where, 'accountId', self::getLoginAccountId() );
        $totalWhere = $where;

        
        if (self::checkValidThaiDate( getMyProp( $criDatas, 'date'), "วันที่ (จาก)") &&
            self::checkValidThaiDate( getMyProp( $criDatas, 'toDate'), "วันที่ (ถึง)")
        ) {
            $where = MongoHelper::appendWhereDateRange($where, 'alarmDate',  
                    getMyProp( $criDatas, 'date'), getMyProp( $criDatas, 'toDate'), false);
        }
        
        if ( getMyProp( $criDatas, 'ackBy') == Rdb::$YES) {
            $where = MongoHelper::appendWhereNotNull($where, 'ackBy');
        }
        else if (getMyProp( $criDatas, 'ackBy') == Rdb::$NO) {
            $where = MongoHelper::appendWhereNull($where, 'ackBy');
        }

                    
        $columns = array( 'id', 'alarmDate', 'vehicleId', "licensePlate", "checkType", 'monitorTopic',
            'alarmType', "message", 'ackBy');
         
        $output = MongoTable::getOutput(
             self::$TABLE_NAME, $columns , array( "where" => $where, "totalWhere" => $totalWhere)
        );
        
        foreach ($output["aaData"] as &$row)
        {
            $mongoId = MongoHelper::getIdByObject($row['_id']);
            $row['mongoId'] =  $mongoId;
            $row['alarmDate'] = (!empty($row['alarmDate']))? DateHelper::mongoDateToThai($row['alarmDate']):"";    
            $row['ackBy'] = self::formatAckBy($row['ackBy'], $mongoId);
        }
        
        $output["message"] = self::errors();
        
        return $output;
    }

    
    //==========================================
    
    private static function formatAckBy($ackBy, $mongoId) {
        if (!empty($ackBy)) {
            return $ackBy;
        }
        
        $ackButtonId = "ackButton_".$mongoId;
        $ackContainer = "ackContainer_".$mongoId;
        
        return "<div  id=\"$ackContainer\"  ><input type='button' style='height:20px;font-size:12px'  " .
              " value='รับทราบ' href='javascript:void(0);' onclick='updateAckBy(\"$mongoId\")' id=\"$ackButtonId\"  /></div>";
    }


    public static function getData($keyId) {
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
    
        $query = DB::table(self::$TABLE_NAME);
        $query->where("_id", $keyId);
    
        $result = $query->first();

        return $result;
    }
 
    
    
    //==========================================
    
    
    public static function addData($data) {            
      //  $data['alarmDate'] = MongoHelper::date();
      //  $data['accountId'] = self::getLoginAccountId();
        
        $data['ackBy'] = null;
        
        $importId = DB::table(self::$TABLE_NAME)->insertGetId($data);
        return $importId;
    }   
    

    public static function editData($keyId, $data) {
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($objectId)) return false;
        
    
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($data);
        return true;
    }   
    
    public static function updateAckBy($keyId, $ackBy) {
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($objectId) || empty($ackBy)) return false;
        
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update( array("ackBy" => $ackBy) );
        return true;
    }   

    
    
    public static function isExistItemKey($itemKey) {
        
        $query = DB::table(self::$TABLE_NAME);
        $query->where("itemKey", $itemKey);
    
        $result = $query->first();

        if (!empty($result)) {
            return true;
        }
        return false;
    }
    
    public static function isExistItemKeyByCri($vehicleId, $monitorTopic, $alarmType, $destValue) {
        $itemKey = self::formatItemKey($vehicleId, $monitorTopic, $alarmType, $destValue);
        return self::isExistItemKey($itemKey);
    }
    
    public static function formatItemKey($vehicleId, $monitorTopic, $alarmType, $destValue) {
       return $vehicleId."_".$monitorTopic."_".$alarmType."_".$destValue;
    }
    
}




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


class MonitorPlan extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'monitor_plan';
    
    protected $primaryKey = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    private static $allDataInSystem;
    
    public static function getDataTable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        $error = "";        
            
        $where = MongoHelper::appendWhere($where, 'accountId', self::getLoginAccountId() );
        $totalWhere = $where;
        
        $where = MongoHelper::appendWhereLike($where, 'name', $criDatas['name'] );
        $where = MongoHelper::appendWhere($where, 'active', $criDatas['active'] );
        
        if (self::checkValidThaiDate($criDatas['date'], "วันที่ (จาก)") &&
            self::checkValidThaiDate($criDatas['toDate'], "วันที่ (ถึง)")
        ) {
            $where = MongoHelper::appendWhereDateRange($where, 'createdAt', $criDatas['date'], $criDatas['toDate']);
        }
        
        
        $columns = array( '_id', 'name', 'active', 'createdAt' );
         
        $output = MongoTable::getOutput(
             self::$TABLE_NAME, $columns , array( "where" => $where, "totalWhere" => $totalWhere)
        );
        
        foreach ($output["aaData"] as &$row)
        {
            $row['mongoId'] =  MongoHelper::getIdByObject($row['_id']);
            $row['name'] = (!empty($row['name']))?$row['name']: "";          
            $row['active'] =  (!empty($row['active']))? Rdb::getActive($row['active']): "";
            $row['createdAt'] = (!empty($row['createdAt']))? DateHelper::mongoDateToThai($row['createdAt']):"";

        }
        
        $output["message"] = self::errors();
        
        return $output;
    }
    
    
    public static function getData($keyId, $onlyActive = false) {
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
    
        $query = DB::table(self::$TABLE_NAME);
        $query->where("_id", $keyId);
    
        if ($onlyActive) {
            $query->where('active', Rdb::$YES);
        }
        $result = $query->first();
        if (!empty($result)) {
            $result['mongoId'] = MongoHelper::getIdByObject($result['_id']);
            $result['detailDatas'] = self::prepareDetailDatasForGet($result);                
        }
        return $result;
    }
 
    
    public static function getAllDataArray() {
        if (is_null(self::$allDataArray)) {

            $query = DB::table(self::$TABLE_NAME);
            $query->where('accountId', self::getLoginAccountId());            
            $query->where('active', Rdb::$YES);
            $query->orderBy("name", "asc");
            self::$allDataArray =  $query->get();
        }
    
        return self::$allDataArray;
    }    
    
    public static function getAllDataInSystem() {
        if (is_null(self::$allDataInSystem)) {
            $query = DB::table(self::$TABLE_NAME);           
            $query->where('active', Rdb::$YES);
            $query->orderBy("name", "asc");
            self::$allDataInSystem =  $query->get();
        }
    
        return self::$allDataInSystem;
    }
    
    
    public static function getAllDataForView() {
        $datas = self::getAllDataArray();
        $output = array();
        
        foreach ($datas as $result) {
            $result['mongoId'] = MongoHelper::getIdByObject($result['_id']);
            $result['detailDatas'] = self::prepareDetailDatasForGet($result); 
            $output[] = $result;
        }
        
        return $output;
    }
   
    public static function getDropdownOption() {
        $results = self::getAllDataArray();
        $option = array();
        $option[''] = DropdownMgr::$textChoose;
    
        foreach ($results as $row) {
            if (isset($row['name'])) {
                $mongoId = MongoHelper::getIdByObject($row['_id']);
                $option[$mongoId] = $row['name'];
            }
        }
        return $option;
    }  
    
    public static function  getDataName($id) {
        if (empty($id)) return "";
        return Rdb::findPropByMongoId(self::getAllDataArray(), "name", $id);
    }
    
    
    public static function  getDataNameForReport($id) {
        if (empty($id)) return "ทั้งหมด";
        return Rdb::findPropByMongoId(self::getAllDataArray(), "name", $id);
    }
    

    
    //=========================================================================================
    //
    // PREPARE
    //
    //=========================================================================================
    
    
    public static function isExist($keyId) {
        return DB::table(self::$TABLE_NAME)->where('_id', '=', MongoHelper::getObjectId($keyId) )->exists();
    }
        
    public static function addData($data, $detailDatas)
    {
        $data['accountId'] = self::getLoginAccountId();
        $data['createdAt'] = MongoHelper::date();
        $data['active'] = Rdb::$YES;      
        
        $data['detailDatas'] = self::prepareDetailDatasForSave($detailDatas);        
        $result = DB::table(self::$TABLE_NAME)->insertGetId($data);     
        return true;
    }   
    

    public static function editData($keyId, $data, $detailDatas) {
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($objectId)) return false;
        
        $data['detailDatas'] = self::prepareDetailDatasForSave($detailDatas);
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($data);
        return true;
    }   
    
    
    public static function deleteData($keyId) {
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
        
        DB::table(self::$TABLE_NAME)->where('_id', $keyId )->delete();
        return true;
    }    
    
    
    //=========================================================================================
    //
    // PREPARE
    //
    //=========================================================================================
    

    
    public static function prepareDetailDatasForGet($output) {
        $rets = array();
        $datas = (isset($output['detailDatas']))? $output['detailDatas']: array();
    
    
        foreach ($datas as $data) {
            $dataType = getMyProp($data, 'dataType', '');
            
            $rets[]= array(
                    "itemNo" =>  getMyProp($data, 'itemNo', ''),                 
                    "itemName" => getMyProp($data, 'itemName', ''),  
                    "itemCode" =>  getMyProp($data, 'itemCode', ''),  
                    "monitorTopic" => getMyProp($data, 'monitorTopic', ''),    
                    "dataType" => $dataType, 

                    "warnAmount" =>  getMyProp($data, 'warnAmount', ''), 
                    "alertAmount" =>  getMyProp($data, 'alertAmount', ''), 
                    "dataTypeDesc" => !empty($dataType)? Rdb::getMonitorDataType($dataType) : "",                 
            );
        }
        return $rets;
    }
    
    private static function prepareDetailDatasForSave($datas) {
        $rets = array();
        if (empty($datas)) return $rets;
            
        $order = 1;
        foreach ($datas as $data) {
            $rets[] = array(
                    "itemNo" =>  $order, 
                    "itemName" => getMyProp($data, 'itemName', ''),  
                    "itemCode" =>  getMyProp($data, 'itemCode', ''),  
                    "monitorTopic" => getMyProp($data, 'monitorTopic', ''),    
                    "dataType" => getMyProp($data, 'dataType', ''), 
                
                    "warnAmount" =>  getMyProp($data, 'warnAmount', ''), 
                    "alertAmount" =>  getMyProp($data, 'alertAmount', ''),        
            );
            $order++;
        }
        return $rets;
    }
    

    
}




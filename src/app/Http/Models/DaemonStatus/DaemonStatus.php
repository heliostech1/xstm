<?php

namespace App\Http\Models\DaemonStatus;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;


class DaemonStatus extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'daemon_status';
    static protected $PRIMARY_KEY = 'daemon_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDatatable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $error = "";        
        
        $columns = array('_id', 'daemon_id', 'start_date', 'end_date', 'result',
            'status', 'run_count');

        $output = MongoTable::getOutput(
             self::$TABLE_NAME,  $columns, array( "where" => $where, "dftOrder" => " daemon_id ASC ")
        );
        
        
        
        foreach ($output["aaData"] as &$row)
        {
            $row['mongoId'] =  MongoHelper::getIdByObject($row['_id']);                
            $row['start_date'] =  (!empty($row['start_date']))? DateHelper::mongoDateToThai($row['start_date']): "";
            $row['end_date'] =  (!empty($row['end_date']))? DateHelper::mongoDateToThai($row['end_date']): "";
         
        }
        
        $output["message"] = self::errors();
        
        return $output;
    }
    

    
    public static function getData($keyId) {                
        if (empty($keyId)) return false;
    
        $query = DB::table(self::$TABLE_NAME);
        $query->where("daemon_id",  $keyId  );
   
        $result = $query->first();
        return $result;
    }

    
    public static function setStartInfo($keyId) {
        $data = self::getData($keyId);
        
        if (empty($data)) {
            $insertData = array(
                "daemon_id" => $keyId, 
                "start_date" => MongoHelper::date(),
                "end_date" => null,
                "status" => "working",
                "result" => "",
                "run_count" => 1
            );
            DB::table(self::$TABLE_NAME)->insert($insertData);
        }
        else {
            $runCount = $data['run_count'] + 1;
            $updateData = array(
                "start_date" =>  MongoHelper::date(),
                "end_date" => null,
                "status" => "working",
                "result" => "",
                "run_count" => $runCount
            );
           DB::table(self::$TABLE_NAME)->where('daemon_id', $keyId )->update($updateData);
        }
        
    }
    
    public static function setEndInfo($keyId) {
        
        $updateData = array(
            "end_date" => MongoHelper::date(),
            "status" => "finish",
        );
        

        DB::table(self::$TABLE_NAME)->where('daemon_id', $keyId )->update($updateData);
           
    }
    
    
    public static function setResult($keyId, $result) {
        
        $updateData = array(
            "result" => $result,
        );
        
        DB::table(self::$TABLE_NAME)->where('daemon_id', $keyId )->update($updateData);
           
    }
    
    public static function isWorking($keyId) {
        
        $data = self::getData($keyId);
        if (!empty($data) && $data['status'] == "working") {
            return true;
        }
        return false;
           
    }
    
    
}




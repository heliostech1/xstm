<?php

namespace App\Http\Models\ImportStatus;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;


class ImportStatus extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'import_status';
    static protected $PRIMARY_KEY = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDatatable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $error = "";        
        
        $columns = array( 'shop_id', 'accountId', 'check_date', 'new_count', 'update_count', 'result',
            'last_item_update_time', 'last_item_create_time');
         
 
        $output = MongoTable::getOutput(
             self::$TABLE_NAME,  $columns, array( "where" => $where, "dftOrder" => "  shop_id ASC ")
        );
        
        foreach ($output["aaData"] as &$row)
        {
            $row['check_date'] =  (!empty($row['check_date']))? DateHelper::mongoDateToThai($row['check_date']): "";
            $row['last_item_update_time'] =  (!empty($row['last_item_update_time']))? DateHelper::mongoDateToThai($row['last_item_update_time']): "";     
            $row['last_item_create_time'] =  (!empty($row['last_item_create_time']))? DateHelper::mongoDateToThai($row['last_item_create_time']): "";              
        }
        
        $output["message"] = self::errors();
        
        return $output;
    }
    
    public static function prepareData($shopId) {      
        $data = self::getData($shopId);
        if (empty($data)) {
            $insertData = array();
            $insertData['shop_id'] = $shopId;              
            $result = DB::table(self::$TABLE_NAME)->insert($insertData);     
        }
        return true;
    }    
    
    public static function addError($shopId, $error) {                
        if (empty($shopId)) return false;
    
        $updateData = array(
            "check_date" => MongoHelper::date(),
            "result" => $error,            
        );
         
        $where = array(
            "shop_id" => $shopId,
        );
        DB::table(self::$TABLE_NAME)->where( $where )->update($updateData);
        return true;

    }

    public static function addSuccess($shopId, $data) {                
        if ( empty($shopId)) return false;
    
        $updateData = $data;
        $updateData['check_date'] =  MongoHelper::date();
         
        $where = array(
            "shop_id" => $shopId,
        );
        DB::table(self::$TABLE_NAME)->where( $where )->update($updateData);
        return true;

    }
    
    public static function getData($shopId) {                
        if (empty($shopId)) return false;
    
        $query = DB::table(self::$TABLE_NAME);
        $query->where("shop_id",  $shopId  );
        
        $result = $query->first();
        return $result;
    }


   
}




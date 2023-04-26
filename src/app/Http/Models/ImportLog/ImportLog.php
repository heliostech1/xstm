<?php

namespace App\Http\Models\ImportLog;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;


class ImportLog extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'import_log';
    static protected $PRIMARY_KEY = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDatatable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        $error = "";        
               
        $totalWhere = $where;
        //$where = MongoHelper::appendWhereLike($where, 'import_type', $criDatas['import_type'] );
        
        
        if (self::checkValidThaiDate($criDatas['date'], "วันที่ (จาก)") &&
            self::checkValidThaiDate($criDatas['to_date'], "วันที่ (ถึง)")
        ) {
            $where = MongoHelper::appendWhereDateRange($where, 'import_date', $criDatas['date'], $criDatas['to_date'], false);
        }
        
    
        
        $columns = array( '_id', 'import_type', 'import_date',  'shop_id', 'accountId', 'item_id', 'item_status',  'item_import_type',
            'item_update_time' );
         

        $output = MongoTable::getOutput(
             self::$TABLE_NAME,  $columns, array( "where" => $where, "dftOrder" => " import_date DESC ")
        );
        
        
        foreach ($output["aaData"] as &$row)
        {
            $row['mongoId'] =  MongoHelper::getIdByObject($row['_id']);              
            $row['import_date'] =  (!empty($row['import_date']))? DateHelper::mongoDateToThai($row['import_date']): "";
            $row['item_update_time'] =  (!empty($row['item_update_time']))? DateHelper::mongoDateToThai($row['item_update_time']): "";                     
        }
        
        $output["message"] = self::errors();
        
        return $output;
    }
    
    //==========================================
    
    /*
    public static function addData($data) {            
        $data['import_date'] = MongoHelper::date();
        
        $importId = DB::table(self::$TABLE_NAME)->insertGetId($data);
        return $importId;
    } 
     */  
    
    /**
       $importedData = array(
               'import_type' => Rdb::$IMPORT_TYPE_SHOPEE_ORDER,
               'import_date' => MongoHelper::date(),
               'item_id' => $orderSn,
               'item_status' => $orderData['order_status'],
               'item_update_time' => DateHelper::timestampToMongoDate( $orderData['update_time'] ),
               "shop_id" => $shopId,
               "accountId" => $accountId,
               "branchId" => $branchId,
           );
       $importedData['item_import_type'] = "add";  
     */
    public static function addDatas($datas) {  
        DB::table(self::$TABLE_NAME)->insert($datas);
        return true;
    }    

}




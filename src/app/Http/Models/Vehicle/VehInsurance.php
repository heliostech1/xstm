<?php

namespace App\Http\Models\Vehicle;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Models\Core\MongoCounter;

class VehInsurance extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'vehicle';
    
    protected $primaryKey = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDataTable($request, $criDatas) {
        $output = array();

        return $output;
    }
    
    

    //======================================================================
    
    
    
    public static function editData($keyId, $data) {
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($objectId)) return false;
        
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($data);
        return true;
    }   
    
    

   
    
}




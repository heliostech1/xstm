<?php

namespace App\Http\Models\Common;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;

class GoodsContainer extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'goods_container';
    
    protected $primaryKey = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDataTable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        $error = "";        
            
        $where = MongoHelper::appendWhere($where, 'accountId', self::getLoginAccountId() );
        $totalWhere = $where;
        
        $where = MongoHelper::appendWhereLike($where, 'name', $criDatas['name'] );
        $where = MongoHelper::appendWhere($where, 'active', $criDatas['active'] );
        
        $columns = array( '_id', 'name', 'active', 'createdAt');
         
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
        }
        return $result;
    }

    public static function getAllDataArray($all=false) {
        if (is_null(self::$allDataArray)) {
            $query = DB::table(self::$TABLE_NAME);
            $query->where('accountId', self::getLoginAccountId());
            if (!$all) {
                $query->where('active', Rdb::$YES);
            }            
            $query->orderBy("name", "asc");
            self::$allDataArray =  $query->get();
        }    
        return self::$allDataArray;
    }    
    
   
    
    public static function  getDataName($id) {
        if (empty($id)) return "";
        return Rdb::findPropByMongoId(self::getAllDataArray(true), "name", $id);
    }
    
    
    public static function isExist($keyId) {
        return DB::table(self::$TABLE_NAME)->where('_id', '=', MongoHelper::getObjectId($keyId) )->exists();
    }
        
    public static function addData($data)
    {
        $data['accountId'] = self::getLoginAccountId();
        $data['createdAt'] = MongoHelper::date();
        $data['active'] = Rdb::$YES;        
        $result = DB::table(self::$TABLE_NAME)->insertGetId($data);     
        return true;
    }   
    

    public static function editData($keyId, $data) {
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($objectId)) return false;
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($data);
        return true;
    }   
    
    
    public static function deleteData($keyId) {
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
        
        DB::table(self::$TABLE_NAME)->where('_id', $keyId )->delete();
        return true;
    }    
    

}




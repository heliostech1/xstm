<?php

namespace App\Http\Models;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Models\Core\DataTable;
use App\Http\Libraries\SqlHelper;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Models\Core\MongoCounter;
use App\Http\Models\Rdb;
use App\Http\Libraries\FormatHelper;


class Branch extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'branch';
    
    protected $primaryKey = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDataTable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        $error = "";        
            
        //DataHelper::debug($criDatas);
       
        $where = MongoHelper::appendWhere($where, 'accountId', static::getLoginAccountId());
        $where = MongoHelper::appendWhereNot($where, 'deleted', Rdb::$YES );
        
        $totalWhere = $where;
        $where = MongoHelper::appendWhere($where, 'branch_code', $criDatas['branch_code']  );
        $where = MongoHelper::appendWhereLike($where, 'name', $criDatas['name'] );
        $where = MongoHelper::appendWhere($where, 'active', $criDatas['active'] );
        
        $columns = array( '_id', 'branch_code', 'name', 'active', 'createdAt');
         
        $output = MongoTable::getOutput(
             self::$TABLE_NAME, $columns , array( "where" => $where, "totalWhere" => $totalWhere)
        );
        
        foreach ($output["aaData"] as &$row)
        {
            $row['mongoId'] =  MongoHelper::getIdByObject($row['_id']);
            $row['branch_code'] = (!empty($row['branch_code']))?$row['branch_code']: "";
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
    
    public static function getAllDataArray($accountId="") {
        if (is_null(self::$allDataArray) || !empty($accountId)) {
            $accountId = empty($accountId)? self::getLoginAccountId(): $accountId;
            
            $query = DB::table(self::$TABLE_NAME);
            $query->where("accountId", $accountId);
            $query->where('active', Rdb::$YES);
            $query->where('deleted', '!=' , Rdb::$YES);
            $query->orderBy("branch_code", "asc");            
            self::$allDataArray =  $query->get();
        }
    
        return self::$allDataArray;
    }
    
    
    public static function getAllDataForUpdateTask($accountId) {
        $query = DB::table(self::$TABLE_NAME);
        $query->where("accountId", $accountId);
        $query->where('active', Rdb::$YES);
        $query->orderBy("branch_code", "asc");            
        return $query->get();
    }    
    
    public static function  getDataName($id) {
        if (empty($id)) return "";
        return Rdb::findPropByMongoId(self::getAllDataArray(), "name", $id);
    }    
    
    public static function  getDataCode($id) {
        if (empty($id)) return "";
        return Rdb::findPropByMongoId(self::getAllDataArray(), "branch_code", $id);
    }    
    
    
    public static function getDatasForChoosePopup() {
        $output = array();
        $datas = self::getAllDataArray();

        foreach ($datas as $data) {

            $output[] = array(
                    "branchId" => MongoHelper::getIdByObject($data['_id']),
                    "name" =>  ($data['name']),
                    "short_name" =>  !empty($data['name'])? FormatHelper::truncate($data['name'], 2, true, ""):"",
            );

        }
        
        return $output;
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
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
        
        //DB::table(self::$TABLE_NAME)->where('_id', $objectId )->delete();
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update( array("deleted" => "Y") );        
        return true;
    }    
    

}




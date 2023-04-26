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

class Account extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'account';
    
    protected $primaryKey = 'accountId';
    
    public $incrementing = false;
    public $timestamps = false;
    
    
    public static function getDataTable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        
        //DataHelper::debug($criDatas);
       
        if (!self::isSysadminAccount()) {
            $where = MongoHelper::appendWhere($where, 'accountId', self::getLoginAccountId());
        }
        
        $totalWhere = $where;
        $where = MongoHelper::appendWhereLike($where, 'accountId', $criDatas['accountId']);
        //$where = MongoHelper::appendWhereLike($where, 'description',"sff");
        

        
        $columns = array( 'accountId', 'description', 'contactName', 'contactPhone', 'contactEmail', 'active');
         
        $output = MongoTable::getOutput(
             self::$TABLE_NAME, $columns , array( "where" => $where, "totalWhere" => $totalWhere)
        );
        
        foreach ($output["aaData"] as &$row)
        {
            $row['active'] = !empty($row['active'])? Rdb::getActive($row['active']) : "";
        }
        
        return $output;
    }
    
    

    public static function getData($key, $onlyActive = false) {
        if (empty($key)) return false;
    
        $query = DB::table(self::$TABLE_NAME);
        $query->where("accountId", $key);
    
        if ($onlyActive) {
            $query->where('active', Rdb::$YES);
        }
        return $query->first();
    }
    
    public static function getAllData($onlyActive = true) {
    
        $query = DB::table(self::$TABLE_NAME);
    
        if ($onlyActive) {
            $query->where('active', Rdb::$YES);
        }
        return $query->get();
    }
    
    public static function getAccountIdList() {
        $datas = self::getAllData();
        $ids = array();
         
        //DataHelper::debug($plans);
        foreach ($datas as $data) {
            if (isset($data['accountId'])) {
                $ids[] = $data['accountId'];
            }  
        }
        return $ids;
    }

    
    
    public static function addData($keyId, $datas, $userDatas)
    {
        if (static::isExist($keyId)) {
            static::setError('ชื่อบัญชีนี้  มีอยู่แล้วในระบบ');
            return FALSE;
        }
        
        if (!empty($userDatas['userId']) && !empty($userDatas['password'])) {
            User::addDefaultUserForAccount(
                    $keyId, $userDatas['userId'], $userDatas['password']);
        }
         
        
        //$datas['createdAt'] = DateHelper::nowSql();
        $datas['active'] = Rdb::$YES;        
    
        DB::table(self::$TABLE_NAME)->insert($datas);
        return true;
    }   
    
    public static function isExist($keyId) {
        return DB::table(self::$TABLE_NAME)->where('accountId', '=', $keyId)->exists();
    }
    
    public static function editData($keyId, $data, $userDatas) {
        if (empty($keyId) || static::protectSysadminAccount($keyId)) return false;
        
       // $datas['updated_at'] = DateHelper::nowSql();
    
        if (!empty($userDatas) && !empty($userDatas['userId']) && !empty($userDatas['password'])) {
            User::addDefaultUserForAccount(
                    $keyId, $userDatas['userId'], $userDatas['password']);
        }       
        
        DB::table(self::$TABLE_NAME)->where('accountId', $keyId)->update($data);
        return true;
    }   
    
    public static function deleteData($keyId) {
        if (empty($keyId) || static::protectSysadminAccount($keyId) ) return false;
        
        DB::table(self::$TABLE_NAME)->where('accountId', $keyId)->delete();
        return true;
    }    
    
    public static function protectSysadminAccount($keyId) {
        if ($keyId == Rdb::$ACCOUNT_SYSADMIN) {
            static::setError("ไม่สามารถแก้ไขหรือลบ sysadmin");
            return true;
        }
        return false;
    }
    
    public static function changeSysadminPassword($accountId, $old, $new) {
         
        $account = static::getData($accountId);
    
        if ($account && DataHelper::isPasswordEqual($account['systemAdminPassword'], $old)) {
            
            DB::table(self::$TABLE_NAME)->where('accountId', $accountId)->update( 
                      array( 'systemAdminPassword' =>  DataHelper::hashPassword($new)  ));
            return true;
        }
    
        static::setError("รหัสผ่านไม่ถูกต้อง");
        return false;
    }
    
}




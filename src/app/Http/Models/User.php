<?php

namespace App\Http\Models;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Models\Core\DataTable;
use App\Http\Libraries\SqlHelper;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;
use App\Http\Models\Core\MongoTable;
use App\Http\Libraries\MongoHelper;

class User extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'user';    
    protected $primaryKey = 'userId';
    
    public $incrementing = false;
    public $timestamps = true;
    
    private static $allDataArray;

    
    public static function getDataTable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        
        //DataHelper::debug($criDatas);
        $where = MongoHelper::appendWhere($where, 'accountId', static::getLoginAccountId());
        
        $totalWhere = $where;
        $where = MongoHelper::appendWhereLike($where, 'userId', $criDatas['userId']);

        $columns = array( 'userGroupId', 'userId', 'description', 'contactName', 'contactPhone', 'contactEmail', 'active');
         
        $output = MongoTable::getOutput(
             "user", $columns, array( "where" => $where, "totalWhere" => $totalWhere)
        );
        
        foreach ($output["aaData"] as &$row)
        {
            $row['userGroupId'] = UserGroup::getUserGroupName($row['userGroupId']);
            //$row['branchId'] = ""; //$this->branch_model->get_branch_name($row['branchId']);
            $row['active'] = !empty($row['active'])? Rdb::getActive($row['active']) : "";
        }
        
        return $output;
    }
    
    public static function getAllDataArray() {
        if (is_null(self::$allDataArray)) {
            $query = DB::table(self::$TABLE_NAME);
            $query->where("accountId", self::getLoginAccountId());
            $query->where('active', Rdb::$YES);
            $query->orderBy("userId", "asc");            
            self::$allDataArray =  $query->get();
        }
    
        return self::$allDataArray;
    }
    
    
    public static function getData($key, $onlyActive = false, $accountId=null) {
        if (empty($key)) return false;
        $accountId = empty($accountId)? static::getLoginAccountId(): $accountId;
    
        $query = DB::table('user');
        $query->where([ ["userId", $key], ["accountId", $accountId] ]);
    
        if ($onlyActive) {
            $query->where('active', Rdb::$YES);
        }
        return $query->first();
    }
    

    public static function addData($userId, $datas)
    {
        if (static::isExist($userId)) {
            static::setError('รหัสผู้ใช้นี้  มีอยู่แล้วในระบบ');
            return FALSE;
        }
        
        $datas['accountId'] = static::getLoginAccountId();
        $datas['createdAt'] = DateHelper::nowSql();
        $datas['active'] = Rdb::$YES;        
    
        DB::table('user')->insert($datas);
        return true;
    }   
    
    public static function addDefaultUserForAccount($accountId, $userId, $password) {
        static::deleteData($userId, $accountId);
        
        $datas = array(
            'accountId'   => $accountId,
            'userId'   => $userId,
            'password'   => DataHelper::hashPassword($password),
            'userGroupId'   => Rdb::$USER_GROUP_ADMIN,
            'createdAt' => DateHelper::nowSql(),
            'active'     => Rdb::$YES,
            'description' => "default user for $accountId",
        );
        
        DB::table('user')->insert($datas);
        return true;
    }
    
    
    public static function isExist($userId, $accountId=null) {
        $accountId = empty($accountId)? static::getLoginAccountId(): $accountId;
        $cond = [['userId', $userId], ['accountId', $accountId] ];
        
        return DB::table('user')->where($cond)->exists();
    }
    
    public static function checkUserPassword($userId, $password, $accountId) {
          $user = static::getData($userId, true, $accountId);

         if ($user && DataHelper::isPasswordEqual($user['password'], $password)) {
             return true;
         }
         return false;
    }
    
    public static function editData($userId, $data) {
        if (empty($userId)) return false;
        
        $datas['updated_at'] = DateHelper::nowSql();
    
        DB::table('user')->where('userId', $userId)->update($data);
        return true;
    }   
    
    public static function deleteData($userId, $accountId=null) {
        if (empty($userId)) return false;
        
        $accountId = empty($accountId)? static::getLoginAccountId(): $accountId;
        $cond = [['userId',  $userId], ['accountId',  $accountId] ];
                
        DB::table('user')->where($cond)->delete();
        return true;
    }    
    
    public static function changePassword($userId, $old, $new) {
         
        $user = static::getData($userId);
    
        //DataHelper::debug($user);
        
        if ($user && DataHelper::isPasswordEqual($user['password'], $old)) {
            
            $cond = [['userId', $userId], ['accountId', static::getLoginAccountId()] ];            
            $data = array( 'password' => DataHelper::hashPassword($new) );
            
            DB::table('user')->where($cond)->update($data);            
            return true;
        }
    
        static::setError("รหัสผ่านไม่ถูกต้อง");      
        return false;
    }    
    
}



<?php

namespace App\Http\Models;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Models\Core\DataTable;
use App\Http\Libraries\SqlHelper;
use App\Http\Libraries\DataHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Libraries\MongoHelper;

class UserGroup extends MyBaseModel
{
    
    protected $table = 'user_group';
    
    protected $primaryKey = 'userGroupId';
    
    public $incrementing = false;
    public $timestamps = false;
   
    private static $userGroupArray;
    
    public static function getDataTable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        
        $totalWhere = $where;
        $where = MongoHelper::appendWhereLike($where, 'userGroupId', $criDatas['userGroupId']);
    
        $columns = array( 'userGroupId', 'name', 'description', 'color');
         
        $output = MongoTable::getOutput( "user_group", $columns, 
            array( "where" => $where, "totalWhere" => $totalWhere, "dftOrder"=> ['orderInList', 'asc'])
        );
    
        foreach ($output["aaData"] as &$row)
        {
            $row['color'] = (!empty($row['color']))? "<div style='background-color:#".$row['color']."'>".$row['color']."</div>":"";
        }
    
        return $output;
    }
    
    
    public static function getUserGroupList() {
        
        if (is_null(self::$userGroupArray)) {
            self::$userGroupArray =  DB::table('user_group')->orderBy('orderInList', 'asc')->get();
        }
        
        return self::$userGroupArray;
    }

    public static function getUserGroupName($id) {
        if ($id == Rdb::$USER_GROUP_SYSADMIN) {
            return Rdb::$USER_GROUP_SYSADMIN_DESC;
        }
        
        $datas = self::getUserGroupList();
        foreach ($datas as $data) {
            if ($data['userGroupId'] == $id) {
                return $data['name'];
            }
        }
        return "";
        
    }
    
    public static function getUserGroupColor($id) {
        $datas = self::getUserGroupList();
        foreach ($datas as $data) {
            if ($data['userGroupId'] == $id) {
                return $data['color'];
            }
        }
        return "";
        
    }
    
    public static function getData($key) {
        if (empty($key)) return false;
    
        $query = DB::table('user_group');
        $query->where("userGroupId", $key);

        return $query->first();
    }
    
    public static function addData($id, $datas)
    {
        if (static::isExist($id)) {
            static::setError("รหัสนี้  '$id' มีอยู่แล้วในระบบ ");
            return FALSE;
        }
    
        $datas['orderInList'] = 99;
        
        DB::table('user_group')->insert($datas);
        return true;
    }
    

    public static function isExist($id) {
        $cond = [ ['userGroupId', $id] ];
    
        return DB::table('user_group')->where($cond)->exists();
    }
    
    public static function editData($id, $datas) {
        if (empty($id)) return false;
    
        DB::table('user_group')->where('userGroupId', $id)->update($datas);
        return true;
    }
    
    public static function deleteData($id) {
        if (empty($id)) return false;
    
        $cond = [['userGroupId',  $id] ];
    
        DB::table('user_group')->where($cond)->delete();
        return true;
    }
    
}



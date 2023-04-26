<?php

namespace App\Http\Models\AppSetting;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;


class AppSettingForUser extends MyBaseModel
{
    
     static protected $TABLE_NAME = 'app_setting_for_user';
    
    protected $primaryKey = 'settingId';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDataTableArray() {
        
        $settingDatas = Rdb::getAppSettingForUser();    
        $dataRows = array();
        $counter = 1;
        foreach ($settingDatas as $settingData) {

            $dataRow = array(
                "columnCounter" => $counter++,
                "settingId" => $settingData['settingId'],
                "name" => $settingData['name'],
                "category" => $settingData['category'],
                "value" => $settingData['defaultValue'],
                "unit" => $settingData['unit'],
            );
            
            $valueData = self::getDataFromCache($settingData['settingId']);
            if (!empty($valueData)) {
                $dataRow['value'] = getMyProp($valueData, 'value', '');
                $dataRow['unit'] = getMyProp($valueData, 'unit', '');
            }
            
            
            $dataRows[] = $dataRow;
        }
        
        return $dataRows;
    }
    
    
    public static function getData($keyId) {
        $settingData = Rdb::getAppSettingForUser($keyId);
        
        $output = array();
        $output['settingId'] = $keyId;
        $output['name'] = getMyProp($settingData, 'name');
        $output['category'] = getMyProp($settingData, 'category');
        $output['value'] = getMyProp($settingData, 'defaultValue');
        $output['unit'] = getMyProp($settingData, 'unit');
        
        $valueData = self::getDataFromCache($keyId);
        if (!empty($valueData)) {
            $output['value'] = getMyProp($valueData, 'value', '');
            $output['unit'] = getMyProp($valueData, 'unit', '');
        }

        return $output;
    }

    
    private static function getDataFromCache($settingId) {
        $allDatas = self::getAllDataArray();
        
        foreach ($allDatas as $data) {
            if ( getMyProp($data, 'settingId') == $settingId) {
                return $data;
            }
        }

        return null;
    }
    
    public static function getAllDataArray() {
        if (is_null(self::$allDataArray)) {

            $query = DB::table(self::$TABLE_NAME);
            $query->where("accountId" , self::getLoginAccountId());
            self::$allDataArray =  $query->get();
        }
    
        return self::$allDataArray;
    }    



    public static function editData($settingId, $data) {
             
        if (empty($settingId)) return false;
        
        $accountId = self::getLoginAccountId();
        $data['accountId'] = $accountId;
        
        $where = array(
            'settingId' => $settingId, 'accountId' => $accountId 
        );
        
        DB::table(self::$TABLE_NAME)->where($where )->delete();
        DB::table(self::$TABLE_NAME)->insertGetId($data);  
        
        return true;
    }   
   
    //===================================================================
    

    public static function getGasTankLifeDays($gasType) {
        //myDebug($gasType);
        $appSetting = null;
        
        if ($gasType == Rdb::$FUEL_LPG) {
            $appSetting = self::getData(Rdb::$APP_SETTING_LPG_TANK_LIFE_YEAR);
        }
        else if ($gasType == Rdb::$FUEL_CNG) {
            $appSetting = self::getData(Rdb::$APP_SETTING_CNG_TANK_LIFE_YEAR);
        }
        
        $value = getMyProp($appSetting, 'value', '');   
        $output = DataHelper::toInteger($value, 0);
        $output = $output * 365;
        return $output;
    }    
    
}



<?php

namespace App\Http\Models\AppSetting;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;


class AppSetting extends MyBaseModel
{
    
     static protected $TABLE_NAME = 'app_setting';
    
    protected $primaryKey = 'settingId';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDataTableArray() {
        
        $settingDatas = Rdb::getAppSetting();    
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
        $settingData = Rdb::getAppSetting($keyId);
        
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
            self::$allDataArray =  $query->get();
        }
    
        return self::$allDataArray;
    }    



    public static function editData($settingId, $data) {
             
        if (empty($settingId)) return false;
        
        DB::table(self::$TABLE_NAME)->where('settingId', $settingId )->delete();
        
        $result = DB::table(self::$TABLE_NAME)->insertGetId($data);  
        return true;
    }   
    

    
    //===================================================
    
    public static function getAppToken() {
        $appSetting = self::getData(Rdb::$APP_SETTING_API_TOKEN);
        $appToken = getMyProp($appSetting, 'value', '');   
        
        return $appToken;
    }
    
    public static function getCovidActiveDays() {
        $appSetting = self::getData(Rdb::$APP_SETTING_COVID_ACTIVE_DAYS);
        $value = getMyProp($appSetting, 'value', '');   
        
        return DataHelper::toInteger($value, 15);
    }
    
    public static function getDisinfectReportActiveDays() {
        $appSetting = self::getData(Rdb::$APP_SETTING_DISINFECT_REPORT_ACTIVE_DAYS);
        $value = getMyProp($appSetting, 'value', '');   
        
        return DataHelper::toInteger($value, 15);
    }
    

    
    public static function isAlarmDebugMode() {
        $appSetting = self::getData(Rdb::$APP_SETTING_ALARM_DEBUG_MODE);
        $value = getMyProp($appSetting, 'value', '');   
        
        return ($value === 1 || $value === "1" )? true: false;
    }
    
    
    
}

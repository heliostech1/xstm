<?php

namespace App\Http\Models\Alarm;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DropdownMgr;


class AlarmSetting extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'alarm_setting';
    static protected $PRIMARY_KEY = 'settingId';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDatatable() {
        $settingDatas = Rdb::getAlarmSetting();    
        $dataRows = array();
        $counter = 1;
        
        foreach ($settingDatas as $settingData) {
            $settingValue = self::getDataFromCache($settingData['name']);
            $enable = getMyProp($settingValue, 'enable', '' ) ;
            
            $dataRows[] = array(
                "columnCounter" => $counter++,
                "settingId" => $settingData['name'],
                "name" => $settingData['description'],
                "enable" =>  !empty($enable)? Rdb::getYesNo( $enable ): "",    
                "alarmTimeForCheckDate" =>  getMyProp($settingValue, 'alarmTimeForCheckDate', '' ), 
                
                "alarmTimeForCheckOdo" =>  getMyProp($settingValue, 'alarmTimeForCheckOdo', '' ),   

            );
            
        }
        
        return $dataRows;
    }
    

    public static function getData($keyId) {
        $output = array();
        $output['settingId'] = $keyId;
        $output['name'] = Rdb::getAlarmSetting($keyId);
        
        $settingValue = self::getDataFromCache($keyId);
        
        $output['enable'] = getMyProp($settingValue, 'enable', '');          
        $output['alarmTimeForCheckDate'] = getMyProp($settingValue, 'alarmTimeForCheckDate', '');    
        $output['alarmTimeForCheckOdo'] = getMyProp($settingValue, 'alarmTimeForCheckOdo', '');  

        
        return $output;
    }
    
    
    public static function getDataFromCache($settingId) {
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
    

    
    public static function editData($keyId, $data) {
             
        if (empty($keyId)) return false;
        DB::table(self::$TABLE_NAME)->where('settingId', $keyId )->delete();
        
        $result = DB::table(self::$TABLE_NAME)->insertGetId($data);  
        return true;
    }   


}




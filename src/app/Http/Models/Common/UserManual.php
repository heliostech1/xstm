<?php

namespace App\Http\Models\Common;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;

class UserManual extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'user_manual';
    
    protected $primaryKey = 'keyId';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    
    public static function getData() {

        $query = DB::table(self::$TABLE_NAME);
        $query->where("keyId", Rdb::$USER_MANUAL_KEY);
    

        $result = $query->first();
        
        if (!empty($result)) {
            $result['keyId'] =  Rdb::$USER_MANUAL_KEY;
            $result['detailDatas'] = self::prepareDetailDatasForGet($result);                
        }
        return $result;
    }
  

    public static function getDataForView() {
        $mainData = self::getData();
        
        $output = array();
        $detailDatas = getMyProp($mainData, 'detailDatas', array()) ;
        $currentTopic = "";
        $counter = 1;
        $memberCounter = 1;
        $groupCounter = 1;   
        
        foreach ($detailDatas as $detailData) {
            $itemNo =  getMyProp($detailData, 'itemNo', '');
            $itemType = getMyProp($detailData, 'itemType', '');   
            $topicName = getMyProp($detailData, 'topicName', '');                  
            $isTopic = self::isTopic($itemType);

            if ($isTopic) {
                $currentTopic = $topicName;  
                $memberCounter = 1;
                $groupCounter++;   
                
                $output[] = array(
                    "columnCounter" => $counter++,       
                    "memberCounter" => "", 
                    "itemId" => self::formatItemId( Rdb::$USER_MANUAL_KEY , $itemNo),
                    "topicName" => $currentTopic,
                    "itemName" => "<b>".$currentTopic."</b>",
                    "itemType" => "",                    
                    "file" => "",      
                    "isTopic" => "Y",
                );       
                
               
            }
            else {
                $output[] = array(
                    "columnCounter" => $counter++,    
                    "memberCounter" => $memberCounter++,  
                    "itemId" => self::formatItemId( Rdb::$USER_MANUAL_KEY , $itemNo),
                    "topicName" => "",
                    "itemName" => "&nbsp;&nbsp;".getMyProp($detailData, 'itemName', ''),
                    "itemType" => getMyProp($detailData, 'itemType', ''),                    
                    "file" => getMyProp($detailData, 'file', ''),  
                    "isTopic" => "",
                );
            }  

        }
        //myDebug($output);
        return $output;                
    }
    
   
    //=========================================================================


    public static function editData($detailDatas) {
        $oldData = self::getData();
        
        $data = array();
        $data['keyId'] = Rdb::$USER_MANUAL_KEY;
        $data['detailDatas'] = self::prepareDetailDatasForSave($detailDatas);
        $data['updatedAt'] = MongoHelper::date();
           
        if (empty($oldData)) {            
           DB::table(self::$TABLE_NAME)->insert($data); 
        }
        else {
           DB::table(self::$TABLE_NAME)->where('keyId', Rdb::$USER_MANUAL_KEY )->update($data);
        }
        
       
        return true;
    }   
    
    
    //=========================================================================================
    //
    // PREPARE
    //
    //=========================================================================================

    
    public static function prepareDetailDatasForGet($output) {
        $rets = array();
        $datas = (isset($output['detailDatas']))? $output['detailDatas']: array();
    
    
        foreach ($datas as $data) {
            $rets[]= array(
                    "itemNo" =>  getMyProp($data, 'itemNo', ''), 
                    "itemType" => getMyProp($data, 'itemType', ''),   
                    "topicName" => getMyProp($data, 'topicName', ''),                  
                    "itemName" => getMyProp($data, 'itemName', ''),  
                    "file" => getMyProp($data, 'file', ''),      
            );
        }
        return $rets;
    }
    
    private static function prepareDetailDatasForSave($datas) {
        $rets = array();
        if (empty($datas)) return $rets;
            
        $order = 1;
        $topicName = "";
        foreach ($datas as $data) {
            
            $itemType = getMyProp($data, 'itemType', '');   
            $itemName = getMyProp($data, 'itemName', '');
            $isTopic = self::isTopic($itemType);
            if ($isTopic) {
                $topicName = $itemName;
            }

            $rets[] = array(
                    "itemNo" =>  $order, 
                    "itemType" => getMyProp($data, 'itemType', ''),   
                    "topicName" => $topicName,                  
                    "itemName" => getMyProp($data, 'itemName', ''),  
                    "file" =>  getMyProp($data, 'file', ''),      
            );
            $order++;
        }
        return $rets;
    }
    
    private static function isTopic($itemType) {
       return  (str_contains($itemType, 'หัวข้อ') || str_contains($itemType, 'กลุ่ม') )? true: false;
    }
    
    private static function formatItemId($profileId, $itemNo) {
       return  $profileId."-".$itemNo;
    }
    
    
}





<?php

namespace App\Http\Libraries\BigPage;

use Closure;
use Log;
use DB;
use App\Http\Models\Rdb;
use App\Http\Models\AppSetting\AppSettingForUser;
use DateTime;
use App\Http\Libraries\MyBaseLib;
use App\Http\Libraries\DateHelper;

class BigPageHelper extends MyBaseLib
{          

    public static function  getResultForGet($fullResult, $prefix, $fields, $arrayFields=array()) {
        $output = array();
        $result = getMyProp($fullResult, $prefix, array());
        
        foreach ($fields as $field) {
            $output[$prefix.$field] = self::convertForGet( getMyProp($result, $field, ""), $field);
        }        
        
        //==========================================================
        foreach ($arrayFields as $arrayField) {
            $name = $arrayField[0];
            $propNames = $arrayField[1];
  
            $output[$prefix.$name] = array();
            $datas = (isset($result[$name]))? $result[$name]: array();
            
            foreach ($datas as $data) {
                $itemData = array();
                
                foreach ($propNames as $propName) {
                    $itemData[$propName] = self::convertForGet(  getMyProp($data, $propName, ""), $propName);
                }
                
                $output[$prefix.$name][]= $itemData;
            }

        }  
        
        //myDebug($fullResult);
        return $output;
    } 
    
    
    public static function  getRequestForSave($request, $prefix, $fields, $arrayFields=array()) {     
        $output = array();
      
        foreach ($fields as $field) {
            $output[$field] = self::convertForSave( $request->input($prefix.$field), $field) ;
        }
        
        //==========================================================
        foreach ($arrayFields as $arrayField) {
            $name = $arrayField[0];
            $propNames = $arrayField[1];
            
            $datas = json_decode($request->input($prefix.$name));

            // myDebug($datas);
            
            if (!empty($datas)) {
                foreach ($datas as $data) {
                    $itemData = array();

                    $data = self::autoFillRequestForSave($output, $prefix, $name, $data);

                    foreach ($propNames as $propName) {
                        $itemData[$propName] = self::convertForSave(  getMyProp($data, $propName, ""), $propName);
                    }

                    $output[$name][]= $itemData;
                }
            }
        }  

        
        return $output;        

    } 

    
    public static function  collectRequest($request, $prefix, $fields, $arrayFields=array()) {
        $output = array();

        foreach ($fields as $field) {
            $output[$prefix.$field] = $request->input($prefix.$field);
        }
        
        //==========================================================
        foreach ($arrayFields as $arrayField) {
            $name = $arrayField[0];
            //$propNames = $arrayField[1];
            
            $output[$prefix.$name] = json_decode( $request->input($prefix.$name) );
        }
        
        
        return $output;   
     
    }    
    
    
    public static function convertForSave($data, $fieldName) {
        $last4Str = substr($fieldName, -4);
  
        if ($last4Str == "Date") {
            return DateHelper::thaiToMongoDate($data);
        }
        
        return $data;        
    }
    
    public static function convertForGet($data, $fieldName) {
        $last4Str = substr($fieldName, -4);
  
        if ($last4Str == "Date") {
            return DateHelper::mongoDateToThai($data, false);
        }
        
        return $data;        
    }
    
    //================================================================

    
    protected static function autoFillRequestForSave($otherData, $prefix, $name, $data) {
        
        if ($prefix == "partFuel_" && $name == "gasDatas") { // คำนวนวันหมดอายุถังแก๊สให้อัตโนมัติ
            

            $gasType = getMyProp($otherData, 'gasType');
            $regisDate = getMyProp($data, 'regisDate');
            $expDate = getMyProp($data, 'expDate');
            $gasTankLife = AppSettingForUser::getGasTankLifeDays($gasType);
            
            if (!empty($regisDate) && empty($expDate) && $gasTankLife > 0) {                
                $calExpDate = DateHelper::getBesideThaiDate($regisDate, $gasTankLife );                
                $data->expDate = $calExpDate;
                
                //myDebug(">> $regisDate , $calExpDate");
            }
            
        }        
        
        //========================================================
        
        
        return $data;
    }
    
    
    
    
}
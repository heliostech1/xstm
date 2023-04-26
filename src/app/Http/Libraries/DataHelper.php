<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use App;
use App\Http\Models\Rdb;

class DataHelper extends MyBaseLib
{         
    public static function test($test) {
        return "TEST: $test";
    }
    
    public static function debug($object, $prefix="") {
        static::debugObject($object, $prefix);
    }
    
    public static function debugObject($object, $prefix="") {
        $result = var_export($object, true);
        Log::debug($prefix."::".$result);
    }
    
    public static function emptyObject() {
        return new \stdClass();
    }
    
    public static function getMyProp($data, $prop, $notFound = null) {
        if (empty($data)) return $notFound;
        
        if (is_object($data) && property_exists($data, $prop)) {
           return $data->$prop;
        }
        else if (is_array($data) && isset($data[$prop] ) ) {
           return $data[$prop]; 
        }
        
        return $notFound;
    }
    
    
    static function findInArray($resultArray , $prop, $value) {
        
        foreach ($resultArray as $row) {
            if ($row[$prop] == $value) {
                return $row;
            }
        }
 
        return false;
    }  
    
    static function isTextEmpty($str){
        return (!isset($str) || trim($str) === '');
    }

    public static function logTime($startTimestamp = null) {
        $result = "=========================== LOG TIME: ".DateHelper::nowSql();
        $duration = " ========================";
        $now = DateHelper::now();
        
        if (!empty($startTimestamp)) {
            $duration = "  |  ".DateHelper::secondsToUnit( $now - $startTimestamp );
        }
        
        static::debugObject($result.$duration);
        return $now;
    }
    
    //==============================================================================
    // IMAGE
    
    public static function isImageFile($file_obj){
        $IMG_TYPE = array('image/pjpeg', 'image/jpeg', 'image/gif', 'image/png','image/x-png');
        $file_type = $file_obj['type'];
        return (in_array($file_type, $IMG_TYPE));
    }    
    
    
    /* GET FILE NAME WITH NO EXTENSION */    
    public static function getPureFileName($file_name) {
        $file_info = pathinfo($file_name);
        $pure_file_name = $file_info['filename'];
        return  $pure_file_name;
                
    }
    
    
    //==============================================================================
    // MATH 
    
    public static function add($data1, $data2, $scale=null) {  // บวก           
        $data1 = self::_toNumber($data1);  // self::debug("======= ADD =====");
        $data2 = self::_toNumber($data2);
        $result = $data1+$data2;    
        $result = self::_toScale($result, $scale); 
        return $result;        
    }
    
    public static function sub($data1, $data2, $scale=null) { // ลบ
        $data1 = self::_toNumber($data1);   //self::debug("======= SUB =====");
        $data2 = self::_toNumber($data2);
        $result = $data1 - $data2;
        $result = self::_toScale($result, $scale); 
        return $result; 
    }
    
    public static function mul($data1, $data2, $scale=null) { // คูณ
        $data1 = self::_toNumber($data1, 1);  //self::debug("======= MUL =====");
        $data2 = self::_toNumber($data2, 1);
        $result = $data1 *  $data2;        
        $result = self::_toScale($result, $scale);         
        return $result; 
    }
    
    public static function div($data1, $data2, $scale=null) { // หาร
        $data1 = self::_toNumber($data1);  //self::debug("======= DIV =====");
        $data2 = self::_toNumber($data2);
        $result = $data1;        
        if ($data2 != 0) {
            $result = $data1 / $data2;
        }
        
        $result = self::_toScale($result, $scale);       
        return $result; 
    }
    
    private static function _toNumber ($data, $default=0) {
        if (empty($data)) return $default;        
        
        $data = str_replace(",", "", $data);
        
        if (!is_numeric($data)) {
            return $default;
        }
        
        if ( self::containsDecimal($data) ) {
            return doubleval($data);
        }
                
        return intval($data);
    }
    
    private static function _toScale ($result, $scale) {
        if (!is_null($scale)) {
            $result = number_format($result, $scale ,  "." ,  "");
            $result = self::_toNumber($result);
        }  
        return $result;
    }
    
    private static function containsDecimal( $val )  {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
    
    //===============================================================================
    //
    // PART: PASSWORD ENCODE, DECODE
    //
    //===============================================================================
    
    /**
     * ตรวจสอบรหัสที่ผู้ใช้กรอกว่าถุกต้องหรือไม่
     * @param  $hash_password  เป็น password ที่เก็บใน database ที่ถูกเข้ารหัสแล้ว
     * @param  $password  เป็น password ที่ผู้ใช้กรอกตอน login
     */
    public static function isPasswordEqual($hash_password, $password) {
        if ( empty($hash_password) || empty($password)) {
            return FALSE;
        }
    
        $salt = substr($hash_password, 0, 10);
        return ($hash_password ==  $salt . substr(sha1($salt . $password), 0, -10));
    }
    
    /**
     * เข้ารหัส password โดยเพิ่ม salt เพื่อเพิ่มความปลอดภัยให้มากขึ้น
     * รูปแบบเป็น   salt[10 หลัก] +  sha1($salt + $password)[ไม่เอา 10 หลักหลัง]
     */
    public static function hashPassword($password)
    {
        if (empty($password)) {
            return FALSE;
        }
    
        $salt = static::salt();
        return  $salt . substr(sha1($salt . $password), 0, -10);
    }
     
    
    /**
     * Generates a random salt value.
     **/
    public static function salt()
    {
        return substr(md5(uniqid(rand(), true)), 0, 10);
    }
        

    //===============================================================================
    //
    // PART: UTILITY
    //
    //===============================================================================
    
    // ลบอันเดิม เพิ่มอันใหม่ ไว้ท้ายสุด
    public static function appendNewToArray($data, $arr) {
        if (!is_array($arr) || empty($data)) return $arr;
        
        foreach (array_keys($arr, $data) as $key) {
            unset($arr[$key]);
        }
        
        array_push($arr, $data);
        return $arr;
    }
    
    public static function getLastItemOfArray($arr) {
        if (!is_array($arr)) return $arr;        
        return end($arr);
    }
    
    public static function convertMongoDateToThai($obj, $field, $format=true, $default="") {
         if (!isset($obj[$field])) {
             return $default;   
         }
         return ($format)? DateHelper::mongoDateToThai($obj[$field],false): $obj[$field];
    }
    
    public static function convertMongoDateToSql($obj, $field, $format=true, $default="") {
         if (!isset($obj[$field])) {
             return $default;   
         }
         return ($format)? DateHelper::mongoDateToSql($obj[$field],false): $obj[$field];
    }
    
    public static function convertFormatDecimal($obj, $field, $format=true, $default="") {
         if (!isset($obj[$field])) {
             return $default;   
         }
         return ($format)? FormatHelper::formatDecimal($obj[$field]): $obj[$field];
    }
    
    public static function convertYesNoToCb($data) {
        if ($data == Rdb::$YES || $data == "on" ) { // on คือ chrome checkbox  dafault value 
            return "checked";            
        }
        return "";
    }
    
    public static function convertCbToYesNo($data) {
        //DataHelper::debug($data);                
        if (!empty($data)) {
            return Rdb::$YES;      
        }
        return Rdb::$NO;
    }
    
    //===============================================================================
    //
    // PART: CONVERT, VALIDATE
    //
    //===============================================================================
    
    public static function stringToArray($data, $sep = ",") {
        if (empty($data)) return array();
        return is_array($data)? $data: explode($sep,$data);
    }
    
    public static function stringToIntegerArray($data, $sep = ",") {
        if (empty($data)) return array();
        $items = is_array($data)? $data: explode($sep,$data);
        
        $output = array();
        foreach ($items as $item) {
            if (self::isInteger($item)) {
                $output[] = self::toInteger($item);
            }            
        }
        return $output;        
    }
    
    
    public static function stringToArrayClearEmpty($data) {
        $datas =  static::stringToArray($data);
        $new_datas = array();
    
        foreach ($datas as $data) {
            if (!$this->is_empty_not_zero($data) ) {
                $new_datas[] = $data;
            }
        }
        return $new_datas;
    }
    
    public static function arrayToString($data, $sep = ",") {
        if (empty($data)) return "";
        return is_array($data)? implode($sep, $data): $data;
    }
    
    public static function removedValueInArray( $datas, $value) {
        if (empty($datas)) return $datas;
        
        $output = array();
        foreach ($datas as $data) {
            if ($data != $value) {
                $output[] = $data;
            }
        }
        return $output;
        
    }
    
    public static function removeLessValueInArray( $datas, $value) {
        if (empty($datas) || !self::isInteger($value)) return $datas;
        
        $output = array();
        foreach ($datas as $data) {
            if (self::isInteger($data)) {
                if ($data > $value) {
                    $output[] = $data;
                }
            }
        }
        
        return $output;        
    }
    
    public static function booleanToString($data) {
        if ($data === true || $data === "true") {
            return "true";
        }
        else {
            return "false";
        }
    }
    
    public static function toInteger($data, $default='') {
        if (empty($data) && $data !== "0" && $data !== 0) {
            return $default;
        }
        return intval($data);
    }
    
    public static function toDouble($data, $default='') {
        if (empty($data) && $data !== "0" && $data !== 0) {
            return $default;
        }
        
        $data = str_replace(",", "", $data);
        
        if (!is_numeric($data)) {
            return $default;
        }
        return doubleval($data);
    }
    
    
    public static function trim($data) {
        $data = (!is_null($data))? trim($data): $data;
        return $data;
    }
    
    public static function isTrimEmpty($data) {
        if (is_null($data)) {
            return true;
        }
        $data = trim($data);
        return empty($data);
    }
    
    public static function isEmptyNotZero($data)  {
        return (self::isTrimEmpty($data) && $data !== 0 && $data !== "0");
    }
    
    
    public static function isInteger($str)
    {
        return (bool) preg_match('/^[\-+]?[0-9]+$/', $str);
    }

    public static function isDecimal($str)
    {
        return (bool) preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
    }   
    
    public static function isNumber($str) {
        if (empty($str) && $str !== '0' && $str !== 0) return false;
        return is_numeric($str);
    }        
    
    public static function arrayMergeUnique($array1, $array2) {
        $array1 = self::stringToArray($array1);
        $array2 = self::stringToArray($array2);
        
        return array_unique (array_merge ($array1, $array2)); 
    }      
    
    //============================================================
    
    public static function bchexdec($hex) {
        if (strlen($hex) == 1) {
            return hexdec($hex);
        } else {
            $remain = substr($hex, 0, -1);
            $last = substr($hex, -1);
            return bcadd(bcmul(16, static::bchexdec($remain)), hexdec($last));
        }
    }

    public static function bcdechex($dec) {
        $last = bcmod($dec, 16);
        $remain = bcdiv(bcsub($dec, $last), 16);

        if ($remain == 0) {
            return dechex($last);
        } else {
            return static::bcdechex($remain) . dechex($last);
        }
    }

    /* ffffffff000000000001586f */
    public static function hexIncrement($hex) {
        $dec = bcadd(self::bchexdec($hex), 1);
        return self::bcdechex($dec);
    }

    
    public static function padZero($str, $length) {
        $padChar = '0';
        $output = str_pad($str, $length, $padChar, STR_PAD_LEFT);
        return $output;
    }
    
    public static function fileDatasToString($datas) {
        $output = array();
        if (!empty($datas)) {
            foreach ($datas as $data) {
                $fileName =  getMyProp( $data , 'fileName');
                if (!empty($fileName)) {
                    $output[] = $fileName;
                }
                
            }
        }
        return implode(",", $output);
    }
    
    public static function stringToFileDatas($string) {
        $output = array();
        if (empty($string)) return $output;
        
        $datas = explode(",", $string);
    
        foreach ($datas as $data) {
            $rets[]= array(
               "fileName" => $data, "caption" => "","tags" =>  "",
            );
        }
        return $rets;
    }
    
    public static function toUppperCase($string) {
        if (empty($string)) return $string;        
        return strtoupper($string);
    }
    
    public static function toLowerCase($string) {
        if (empty($string)) return $string;        
        return strtolower($string);
    }      

}



<?php

namespace App\Http\Models\FileUpload;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\FormatHelper;

class FileUpload extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'file_upload';
    
    protected $primaryKey = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDataTable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        $error = "";        
            

        $totalWhere = $where;
        $where = MongoHelper::appendWhereLike($where, 'name', $criDatas['name'] );
 
        if (self::checkValidThaiDate($criDatas['date'], "วันที่ (จาก)") &&
            self::checkValidThaiDate($criDatas['toDate'], "วันที่ (ถึง)")
        ) {
            $where = MongoHelper::appendWhereDateRange($where, 'createdAt', $criDatas['date'], $criDatas['toDate']);
        }
        
        
        $columns = array( '_id', 'name', 'directory', 'createdAt' );
         
        $output = MongoTable::getOutput(
             self::$TABLE_NAME, $columns , array( "where" => $where, "totalWhere" => $totalWhere)
        );
        
        foreach ($output["aaData"] as &$row)
        {
            $row['mongoId'] =  MongoHelper::getIdByObject($row['_id']);
            $row['rawName'] =  getMyProp($row, 'name');                 
            $row['name'] =  FormatHelper::formatImageLink(getMyProp($row, 'name'));           
            $row['directory'] =  getMyProp($row, 'directory','');
            $row['createdAt'] = (!empty($row['createdAt']))? DateHelper::mongoDateToThai($row['createdAt']):"";

        }
        
        $output["message"] = self::errors();
        
        return $output;
    }
    
    
    public static function getData($keyId) {
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
    
        $query = DB::table(self::$TABLE_NAME);
        $query->where("_id", $keyId);
    
        $result = $query->first();
        if (!empty($result)) {
            $result['mongoId'] = MongoHelper::getIdByObject($result['_id']);
        }
        return $result;
    }

    public static function getDataByFileName($fileName) {           
        if (empty($fileName)) return false;
    
        $query = DB::table(self::$TABLE_NAME);
        $query->where("name", $fileName);
    
        $result = $query->first();
        if (!empty($result)) {
            $result['mongoId'] = MongoHelper::getIdByObject($result['_id']);
        }
        return $result;
    }

    
    public static function isExist($keyId) {
        return DB::table(self::$TABLE_NAME)->where('_id', '=', MongoHelper::getObjectId($keyId) )->exists();
    }
        
    public static function addData($name, $directory, $thumbDirectory)
    {
       // $data['accountId'] = self::getLoginAccountId();
        $data['createdAt'] = MongoHelper::date();
        $data['name'] = $name;
        $data['directory'] = $directory;
        $data['thumbDirectory'] = $thumbDirectory;        
        DB::table(self::$TABLE_NAME)->insert($data);     
        return true;
    }   
    
    public static function editData($keyId, $data) {
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($objectId)) return false;
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($data);
        return true;
    }   
    
    
    public static function deleteData($keyId, $fileData) {
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;

        //---------------------------------------------------
        
        $fileName = getMyProp($fileData, 'name');
        $filePath = self::getTargetFilePath($fileName);
        $thumbFilePath = self::getTargetThumbFilePath($fileName);
        
        if (empty($filePath))  {
           self::setError(  "ไม่พบข้อมูลไฟล์ '$fileName' " );
        } 
        else if (!@unlink($filePath)) { 
           self::setError(  "ไม่สามารถลบไฟล์ '$fileName' " );            
        } 

        if (empty($thumbFilePath))  {
           self::setError(  "ไม่พบข้อมูล thumb ไฟล์ '$fileName' " );
        } 
        else if (!@unlink($thumbFilePath)) { 
           self::setError(  "ไม่สามารถลบ thumb ไฟล์ '$fileName' " );            
        } 
        
        DB::table(self::$TABLE_NAME)->where('_id', $keyId )->delete();
        
        return self::hasErrors()? false: true;
    }    
    

    //==========================================================
    

    public  static function getTargetThumbFilePath($fileName) {        
        $data = self::getDataByFileName($fileName);
        $directory = getMyProp($data, 'thumbDirectory', '');
        if (empty($directory) || empty($fileName)) return false;
        
        $root =  config('app.rootStorage');         
        $filePath = $root.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.$fileName;
        return $filePath;        
    }
    
    
    public  static function getTargetFilePath($fileName) {        
        $data = self::getDataByFileName($fileName);
        $directory = getMyProp($data, 'directory', '');
        if (empty($directory) || empty($fileName)) return false;
        
        $root =  config('app.rootStorage');         
        $filePath = $root.DIRECTORY_SEPARATOR.$directory.DIRECTORY_SEPARATOR.$fileName;
        return $filePath;        
    }



}

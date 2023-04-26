<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use App;
use URL;
use App\Http\Models\Rdb;
use Intervention\Image\Facades\Image;
use Intervention\Image\Exception\ImageException;
use App\Http\Libraries\DataHelper;

class FileMgrTmp extends MyBaseLib
{        
    public $app;
    
    public function __construct()
    {  
        //Log::debug("AuthMgr construct");
    }
    
    public static function upload($file, $parentDirName="", $watermarkInfo="", $myExt = "") {
        //self::debug($file);
        ini_set('memory_limit','256M');
        $target = self::getTargetDir($parentDirName);
        
        if (!$target) {
            return false;
        }                
        
        $ext = (!empty($myExt))? $myExt: $file->getClientOriginalExtension();  
        $ext = strtolower($ext);
        
        $sourcePath = $file->getRealPath();
        
        $targetFileName = self::getUploadFileName($ext);        
        $targetPath = $target.DIRECTORY_SEPARATOR.$targetFileName;
        
        //=========================================
        
        if (! move_uploaded_file($sourcePath, $targetPath)) {            
            self::setError( "ไม่สามารถย้ายไฟล์ได้");
            return false;
        } 
        
        if ( self::isImageExt($ext) ) { // 
            
            if (!self::createThumbFile($targetPath, $targetFileName )) {
                return false;            
            }
        }

        return $targetFileName;        

    }
     

    
    private static function createThumbFile($filePath, $fileName) {
        
        $targetThumbDir =  self::getTargetThumbDir();
                
        $fileNameThumb = self::formatThumbFileName($fileName);

        $filePathThumb = $targetThumbDir.DIRECTORY_SEPARATOR.$fileNameThumb;
                
        //http://image.intervention.io/api/make
        
        try {
            $result = Image::make($filePath);
            $result->fit(250);
            $result->save($filePathThumb);
                    
        } catch (ImageException $ex) {
            self::setError( "สร้างไฟล์ผิดพลาด: ". $ex->getMessage());
            return false;
        }
                
        return true;        
    }
    
    //============================================================
    
    public static function isImageExt($ext) {        
        if (in_array( strtolower($ext), array("jpg","jpeg","gif","png" ))) {
            return true;
        }
        return false;
    }
    
    public static function formatThumbFileName($fileName) {
        if (strpos($fileName,  '_thumb') !== false) {
            return $fileName;
        }
        
        $paths = pathinfo($fileName);        
        return $paths['filename']."_thumb.jpg";    //  ".$paths['extension'];     
    }
    
    public static function getUploadFileName($ext = "", $prefixName="") {
        $time = time();    
        $datePart = date('Y', $time).date('m', $time).date('d', $time);
        $timePart = date('H', $time).date('i', $time).date('s', $time);
        $uid = uniqid();
           
        $name = "$datePart-$timePart";
        if (!empty($prefixName)) {
            $name .=  "-$prefixName";
        }
        $name .= "-$uid";
        
        if (!empty($ext)) {
            $name .= ".".$ext;
        }
        
        return $name;
    }
    
    
    //=======================================================================
    // FOR SAVE 
    
    public static function getTargetThumbDir() {
        $thumbDir =  Rdb::$THUMB_DIR_NAME;
        $root =  config('app.rootStorage');  //// D:\_WWW/bsid_uploader               

        $target = $root.DIRECTORY_SEPARATOR.$thumbDir;
        
        if (!file_exists($target)) {
            @mkdir($target, 0777, true);
        }        

        if (!is_dir($target)) {
            self::setError( "ไม่สามารถเข้าถึงโฟล์เดอร์ที่เก็บไฟล์ (".$target.") ");
            return false;
        }
        
        return $target;
    }
    
    public  static function getTargetDir($parentDirName) {        
        $parentDirName = (empty($parentDirName))? Rdb::$ACCOUNT_SYSADMIN: $parentDirName;
        $root =  config('app.rootStorage');  //// D:\_WWW/bsid_uploader               

        $target = $root.DIRECTORY_SEPARATOR.$parentDirName;
        
        if (!file_exists($target)) {
            @mkdir($target, 0777, true);
        }        

        if (!is_dir($target)) {
            self::setError( "ไม่สามารถเข้าถึงโฟล์เดอร์ที่เก็บไฟล์ (".$target.") ");
            return false;
        }
        
        return $target;
    }
    
    //============================================================
    // FOR GET
        
    public  static function getTargetThumbFilePath($filename) { 

         $thumbName = self::formatThumbFileName($filename);
            
        $root =  config('app.rootStorage');         
        $targetDir = $root.DIRECTORY_SEPARATOR.Rdb::$THUMB_DIR_NAME;
        $targetFile = $targetDir.DIRECTORY_SEPARATOR.$thumbName;
        
        if (!file_exists($targetFile)) {
            return false;
        }

        return $targetFile;                
    }       
        
    public  static function getTargetFilePath($filename, $parentDirName) { 
        $parentDirName = (empty($parentDirName))? Rdb::$ACCOUNT_SYSADMIN: $parentDirName;
        
        $root =  config('app.rootStorage');         
        $targetDir = $root.DIRECTORY_SEPARATOR.$parentDirName;
        $targetFile = $targetDir.DIRECTORY_SEPARATOR.$filename;
        
        if (file_exists($targetFile)) {
            return $targetFile;
        }

        
        $subDirs = glob($targetDir.DIRECTORY_SEPARATOR."*", GLOB_ONLYDIR );
        //DataHelper::debug($subDirs);
        
         if (empty($subDirs)) {
             return false;
         }
         
         foreach ($subDirs as $subDir) {
            $targetFile = $subDir.DIRECTORY_SEPARATOR.$filename;
            
            if (file_exists($targetFile)) {
                return $targetFile;
            }
         }
         
        return false;                
    }       
    
    /*
     *   File Name: mysql_n.png
         File Extension: png
         File Real Path: C:\\Windows\\Temp\\php75C4.tmp
         File Size: 605044'  
     */
    public static function debug($file) {
        
        //Display File Name
        $html = "\n File Name: ".$file->getClientOriginalName();
        $html .= "\n File Extension: ".$file->getClientOriginalExtension();
         
        //Display File Real Path
        $html .= "\n File Real Path: ".$file->getRealPath();

        //Display File Size
        $html .= "\n File Size: ".$file->getSize();
         
        //Display File Mime Type
        //$html .= 'File Mime Type: '.$file->getMimeType();
        
        DataHelper::debug($html);
    }
    
}




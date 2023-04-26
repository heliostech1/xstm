<?php

namespace App\Http\Libraries\BigPage;

use Closure;
use Log;
use DB;
use App\Http\Models\Rdb;
use App\Http\Models\AppSetting\AppSettingForUser;
use DateTime;
use App\Http\Libraries\DateHelper;

class StaffHelper extends BigPageHelper
{          

    public static function  hello($data) {
        return "hello";
        //return DB::connection()->getPdo()->quote($data);        
    }
    
    
    //-------------------------------------------------------------------------
    //  BASE

    public static function  getBaseFields() { // ข้อมูลพื้นฐาน
        return array(
            "address", // ที่อยู่
            "phone", // เบอร์โทร  พนักงานขับรถ        
           // "relateDatas", // ข้อมูลผู้ติดต่อฉุกเฉิน
            "workCompany", // สังกัด
            
            "staffType", // ชนิดพนักงาน
            //"vehicleId", // ทะเบียนรถที่ประจำ 
            "fileDatas", //  รูปถ่ายพนักงาน                    
        );
    }
    
    
    public static function  getBaseArrayFields() {

        $relateDatas =  array(  // ข้อมูลผู้ติดต่อฉุกเฉิน
            "name" ,  // ชื่อ
            "detail", // ความสัมพันธ์
            "phone", // เบอร์โทร
            "address", // ที่อยู่            
        ) ;
        
        return array(
            array("relateDatas", $relateDatas)              
        );  

    }
    
    
    //-------------------------------------------------------------------------
    // DRIVER LICENSE
    
    public static function  getLicenseFields() { // ข้อมูลใบขับขี่
        return array();
    }
    
    public static function  getLicenseArrayFields() {

        $mainDatas =  array(
            "licenseType", // ประเภทใบขับขี่
            "issueNo", // ฉบับที่
            "issueDate", // วันอนุญาต
            "expDate", // วันหมดอายุ
            "fileDatas", // ภาพใบขับขี่(แนบไฟล์) 
        );
        
        return array(
            array("mainDatas", $mainDatas),
        );
    }
     
    
    //-------------------------------------------------------------------------
    //  WORK
    
    
    public static function  getWorkFields() { // ข้อมูลการทำงาน
        return array( 
            "startDate", // วันที่เริ่มงาน
            "workStatus", // สถานะการทำงาน  ( ทำงานอยู่, พ้นสภาพพนักงาน)
            "amount", // เงินเดือน       
            "amountDay", // เงินเดือน                  
        );
    }
    
    public static function  getWorkArrayFields() {
        return array();        
    }
    
    
    //-------------------------------------------------------------------------
    // CONTAINER
    
    public static function  getAbsentFields() { // ข้อมูลวันหยุด/ลา
        return array();
    }
    
    public static function  getAbsentArrayFields() {
        return array();        
    } 
    

    
    
}
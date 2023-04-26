<?php

namespace App\Http\Libraries\BigPage;

use Closure;
use Log;
use DB;
use App\Http\Models\Rdb;
use App\Http\Models\AppSetting\AppSettingForUser;
use App\Http\Libraries\DateHelper;

class VehInsuranceHelper extends BigPageHelper
{          

    //-------------------------------------------------------------------------
    // 1. ACT พรบ
    
   
        
    public static function  getInsActArrayFields() {

        $datas =  array(
            "company", // บริษัทประกัน
            "insNo", // กรมธรรม์ประกันภัยเลขที่
            "insPerson", // ชื่อผู้เอาประกันภัย
            "address", // ที่อยู่
            "agreeDate", // วันที่ทำพรบ
            
            "amount", // จำนวนเบี้ยประกัน       
            "insStartDate", // ระยะเวลาประกันภัย  เริ่มต้นวันที่   
            "insEndDate", // ระยะเวลาประกันภัย  ถึงวันที่
            "carCode", // รหัส  (รายการรถยนต์ที่เอาประกันภัย)
            "carName", // ชื่อรถยนต์/รุ่น => เอาข้อมูล model มาเป็น default
            
            "carLicensePlate", // เลขทะเบียน => เอาข้อมูล licensePlate มาเป็น default
            "carBodyNumber", // เลขตัวถัง  => เอาข้อมูล bodyNumber มาเป็น default
            "carBodyType", // แบบตัวถัง   
            "carSize", // จำนวนที่นั่ง/ขนาด/น้ำหนัก
            "fileDatas", // ภาพตารางกรมธรรม์                              
        );
        
        return array(
            array("insActDatas", $datas),
        );
    }

    
    //-------------------------------------------------------------------------
    // 2. CAR ประกันภัยรถยนต์
    

        
    public static function  getInsCarArrayFields() {

        $datas =  array(
            "insType", // ประเภทประกัน
            "insNo", // กรมธรรม์ประกันภัยเลขที่ (Policy No.)
            "company", // ชื่อบริษัทประกันภัย
            "insPerson", // ผู้เอาประกันภัย (The Insured)
            "benefitPerson", // ผู้รับผลประโยชน์ (Beneficiary)
            
            "agreeDate", // วันทำสัญญาประกันภัย (Agreement made on)      
            "issueDate", // วันทำกรมธรรม์ประกันภัย (Policy issued on)    
            "insStartDate", // ระยะเวลาประกันภัย (Period of insurance)   เริ่มต้นวันที่ (From)
            "insEndDate", // ระยะเวลาประกันภัย (Period of insurance)   สิ้นสุดวันที่ (To)  
            "amount", // เบี้ยประกัน
            
            "fundDamage", // ทุนประกัน ความเสียหายต่อรถยนต์( Own Damage)
            "fundLost", // ทุนประกัน รถยนต์สูญหายหรือไฟไหม้ ( Fire and Theft)            
            "fileDatas", // แนบไฟล์กรมธรรม์
                             
        );
        
        return array(
            array("insCarDatas", $datas),
        );
    }
    
    //-------------------------------------------------------------------------
    // 3. GOODS ประกันภัยสินค้า
    
    public static function  getInsGoodsArrayFields() {

        $datas =  array(
            "insType", // ประเภทประกัน
            "insNo", // กรมธรรม์ประกันภัยเลขที่ (Policy No.)
            "company", // ชื่อบริษัทประกันภัย
            "insPerson", // ผู้เอาประกันภัย (The Insured)
            "benefitPerson", // ผู้รับผลประโยชน์ (Beneficiary)
            
            "agreeDate", // วันทำสัญญาประกันภัย (Agreement made on)      
            "issueDate", // วันทำกรมธรรม์ประกันภัย (Policy issued on)    
            "insStartDate", // ระยะเวลาประกันภัย (Period of insurance)   เริ่มต้นวันที่ (From)
            "insEndDate", // ระยะเวลาประกันภัย (Period of insurance)   สิ้นสุดวันที่ (To)  
            "amount", // เบี้ยประกัน
            
            "fund", // ทุนประกัน      
            "fileDatas", // แนบไฟล์กรมธรรม์
                             
        );
        
        return array(
            array("insGoodsDatas", $datas),
        );
    }
    
    //-------------------------------------------------------------------------
    // 4. CLAIM  ประวัติการเคลม Claim

    
    public static function  getClaimArrayFields() {

        $datas =  array(
            "times", // ครั้งที่
            "claimDate", // วันที่
            "claimType", // ชนิดการเคลม  (พรบ ประกันภัยรถยนต์ ประกันภัยสินค้า)
            "insNo", //    เลขที่กรมธรรม์	ดึงจากข้อมูลกรมธรรม์ตามชนิดการเคลม หมายเลขการเคลม
            "claimNo", // หมายเลขการเคลม

            "actDate", // วันเวลาที่เกิดเหตุ  (วัน)  
            "actTime", // วันเวลาที่เกิดเหตุ  (เวลา)   
            "actDriver", // ชื่อผู้ขับขี่ขณะเกิดเหตุ    
            "fixStartDate", // วันที่เข้าซ่อม      
            "fixEndDate", // วันที่ซ่อมเสร็จ
            
            "fixCost", // ค่าใช้จ่ายในการเคลม
            "detail", // บันทึกข้อมูล
            "fileDatas", // แนบเอกสาร
    
          //  "actDateTime", // วันเวลาที่เกิดเหตุ  (วัน เวลา)                
        );
        
        return array(
            array("claimDatas", $datas),
        );
    }
    
    
 
}

/* 

ข้อมูลอุบัติเหตุ-ความเสียหาย
 
ทะเบียนรถ 
ครั้งที่ 
วันที่เกิดเหตุ 
สถานที่เกิดเหตุ 
ชื่อผู้ขับขี่

วันที่เข้าซ่อม
วันที่ซ่อมเสร็จ
ค่าใช้จ่ายในการซ่อมแซม
แนบเอกสาร
 


ข้อมูลการซ่อมบำรุง
 
ทะเบียนรถ 
ครั้งที่ 
วันที่ซ่อมบำรุง 
วันที่ซ่อมเสร็จ 
เลขไมล์ 

รายการซ่อม
ค่าใช้จ่ายในการซ่อม 
การรับประกัน
แนบเอกสาร

 */

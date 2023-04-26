<?php

namespace App\Http\Libraries\BigPage;

use Closure;
use Log;
use DB;
use App\Http\Models\Rdb;
use App\Http\Models\AppSetting\AppSettingForUser;
use DateTime;
use App\Http\Libraries\DateHelper;

class VehicleHelper extends BigPageHelper
{          

    public static function  hello($data) {
        return "hello";
        //return DB::connection()->getPdo()->quote($data);        
    }
    
    
    //-------------------------------------------------------------------------
    // 1. REGIS
    
    public static function  getRegisFields() {
        return array();
    }
    
    public static function  getRegisArrayFields() {

        $regisDatas =  array(
            "regisDate", // วันจดทะเบียน
            "regisNumber", // เลขทะเบียน
            "province", // จังหวัด
            "vehicleType", // ประเภทรถ
            "vehicleRegisType", // ประเภทรถตามจดทะเบียน , รย.
            
            "look", // ลักษณะ      
            "brand", // ยี่ห้อรถ    
            "design", // แบบ      
            "model", // รุ่น
            "color", // สี
            
            "bodyNumber", // เลขตัวรถ
            "address", // อยู่ที่
            "engineBrand", // ยี่ห้อเครื่องยนต์
            "engineNumber", // เลขเครื่องยนต์
            "fuel", // เชื้อเพลิง
            
            "gasTankNumber", // เลขถังแก๊ส  รูปแแบบ string1, string2, ...
            "loop", //  จำนวน (สูบ)
            "cc", //  ซีซี
            "horsePower", //  แรงม้า
            "wheel", //  จำนวนเพลา/ล้อ/ยาง
            
            "carWeight", //  น้ำหนักรถ (กก.)
            "loadWeight", //  น้ำหนักบรรทุก/น้ำหนักลงเพลา (กก.)
            "totalWeight", //  น้ำหนักรวม
            "seat", //  ที่นั่ง
            "fileDatas", //  ภาพข้อมูลจดทะเบียน   รูปแแบบ string1, string2, ...                     
        );
        
        return array(
            array("regisDatas", $regisDatas),
        );
    }
     
    
    
    //-------------------------------------------------------------------------
    // 2. OWNER
    
    public static function  getOwnerFields() {
        return array();
    }
    
    public static function  getOwnerArrayFields() {
        $ownerDatas = array(
            "ownerDate", // วันที่ครอบครองรถ
            "ownerName", // ผู้ถือกรรมสิทธ์
            "ownerBirthDate", // วันเกิด
            "ownerAddress", // ที่อยู่
            "ownerPhone", // โทร.
            
            "holderName", // ผู้ครอบครอง      
            "cardNumber", // เลขที่บัตร    
            "holderBirthDate", // วันเกิด      
            "holderNation", // สัญชาติ
            "holderAddress", // ที่อยู่
            
            "holderPhone", // โทร.
            "leaseContractNumber", // สัญญาเช่าซื้อเลขที่
            "fileDatas", //  ภาพข้อมูลการครอบครองรถ                     
        );
        return array(
            array("ownerDatas", $ownerDatas),
        );        
    }
    
    //-------------------------------------------------------------------------
    // 3. TAX
    
    public static function  getTaxFields() {
        return array();
    }
    
    
    public static function  getTaxArrayFields() {
        $taxDatas = array(
            "taxDate", // วันเสียภาษี
            "dueDate", // วันครบกำหนดเสียภาษี
            "taxAmount", // ค่าภาษี
            "extraAmount", // เงินเพิ่ม
            "fileDatas", //  ภาพรายการเสียภาษี                     
        );
        return array(
            array("taxDatas", $taxDatas),
        );         
    }
    
    
    //-------------------------------------------------------------------------
    // 4. CONTAINER
    
    public static function  getContainerFields() {
        return array(
            "containerType", // ชนิดตู้สินค้า   คอก ตู้ทำความเย็น ตู้แห้ง
            "width", // ขนาดภายใน  กว้าง
            "long", // ขนาดภายใน  ยาว
            "height", // ขนาดภายใน  สูง           
            "airInnerHeight", // ความสูงภายในใต้แอร์ถึงพื้นตู้ (เมตร)
            
            "outerWidth", // ขนาดตู้ภายนอก  กว้าง
            "outerLong", // ขนาดตู้ภายนอก  ยาว
            "outerHeight", // ขนาดตู้ภายนอก  สูง  
            "groundHeight", // ความสูงพื้นรถจากพื้นดิน (เมตร) 
            //"fileDatas", //  ภาพข้อมูลการครอบครองรถ                     
        );
    }
    
    
    //-------------------------------------------------------------------------
    // 5. FUEL

    public static function  getFuelFields() {

        return array(
            "oilType", // ชนิดน้ำมันเชื้อเพลิง , เบนซิน, ดีเซล
            "oilTankSize", // ปริมาตรความจุถังน้ำมัน(ลิตร)
            "gasType", // ชนิดแก๊สเชื้อเพลิง , LPG , CNG
            "gasCount", // จำนวนถังแก๊ส          
            "gasTotalSize", // ปริมาตรความจุถังแก๊สรวม(ลิตร)
            
            "certBy", // ใบรับรองวิศวกร
            "certDate", // ใบรับรองวิศวกร
            "certExpDate", // ใบรับรองวิศวกร
            "fileDatas", //  ภาพข้อมูลใบรับรอ                    
        );
    }
    
    public static function  getFuelArrayFields() {  
        $gasDatas =  array(
            "number" ,  // หมายเลขถัง
            "regisDate", // วันจดทะเบียน
            "expDate" // วันหมดอายุ
        ) ;
        
        return array(
            array("gasDatas", $gasDatas)              
        );
    }  
    
    //-------------------------------------------------------------------------
    // 6. CHILLER
    
    public static function  getChillerFields() {

        return array(
            "brand", // ยี่ห้อเครื่องทำความเย็น
            "model", // Model เครื่องทำความเย็น
            "refrigerant", // ชนิดสารทำความเย็น
            "temperature", // ช่วงอุณหภูมิทำความเย็น                        
        );
    }
    
    public static function  getChillerArrayFields() {

        $examDatas =  array(  // ข้อมูลการสอบเทียบเครื่องทำความเย็น
            "order" ,  // ครั้งที่
            "operateDate", // วันที่สอบเทียบ
            "expDate", // วันหมดอายุ
            "operateBy", // ชื่อผู้สอบเทียบ
            "fileDatas" // แนบเอกสาร            
        ) ;
        
        $mapDatas =  array( // ข้อมูลการทำ mapping เครื่องทำความเย็น
            "order" ,  // ครั้งที่
            "operateDate", // วันที่ทำ mapping
            "expDate", // วันหมดอายุการทำ mapping
            "operateBy", // ชื่อผู้ทำ
            "fileDatas" // แนบเอกสาร  
        ) ;
        
        return array(
            array("examDatas", $examDatas),
            array("mapDatas", $mapDatas),
        );
    }
     
    
    //-------------------------------------------------------------------------
    // 7. CARE

    
    public static function  getCareFields() {

        return array(
            "vehicleCare", // ชื่อผู้ให้บริการ
            "vCareType", // ชนิดของรถ                       
        );
    }
    
    public static function  getCareArrayFields() {

        $driverDatas =  array( // ผู้ชับขี่ประจำรถ 
            "staffId" ,  // รหัส
            "staffName", // ชื่อ
            "phone", // เบอร์โทร            
            "workCompanyId", // สังกัด    
            "workCompanyDesc", // สังกัด   
        ) ;
        
        $workerDatas =  array( // แรงงานประจำรถ  
            "staffId" ,  // รหัส
            "staffName", // ชื่อ
            "phone", // เบอร์โทร            
            "workCompanyId", // สังกัด    
            "workCompanyDesc", // สังกัด              
        ) ;
        
        return array(
            array("driverDatas", $driverDatas),
            array("workerDatas", $workerDatas),
        );
    }
     
    
    //-------------------------------------------------------------------------
    // 8. MONITOR

    
    public static function  getMonitorFields() {
        return array(
            "monitorPlan", // แผนการซ่อมบำรุง             
        );
    }
    
    public static function  getMonitorArrayFields() {

        $monitorDatas =  array(
            "monitorTopic" ,  
            "warnAt",
            "alertAt", 
        ) ;
        
        return array(
            array("monitorDatas", $monitorDatas)              
        );
    }
     
    public static function  getMonitorArrayFieldsForView() {

        $monitorDatas =  array(
            "monitorTopic" ,  
            "warnAt",
            "alertAt", 
            "lastRepairDate",
            "lastRepairOdo",
            "warnStatus",
            "alarmStatus",
        ) ;
        
        return array(
            array("monitorDatas", $monitorDatas)              
        );
    }
    
    //-------------------------------------------------------------
    

    
    public static function getAgeYear($inputDatas) {
        $partRegis = getMyProp($inputDatas, 'partRegis_', '');
        $regisDatas = getMyProp($partRegis, 'regisDatas', '');
  
        if (is_array($regisDatas) && sizeof($regisDatas) > 0) {
            $regisDate = getMyProp($regisDatas[0], 'regisDate', '');
            $regisDateSql = DateHelper::mongoDateToSql($regisDate, false);
            $todaySql = DateHelper::todaySql();
              
            if (empty($regisDateSql) || $regisDateSql > $todaySql) return 0;
            
            $date1 = new DateTime($regisDateSql);
            $date2 = new DateTime($todaySql);
            $interval = $date1->diff($date2);

           // myDebug("difference " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days ");
            return $interval->y;
        }
        
        return 0;
    }
    
    public static function getWorkCompany($inputDatas) {
        $partData = getMyProp($inputDatas, 'partCare_', '');
        $driverDatas = getMyProp($partData, 'driverDatas', '');
        $workerDatas = getMyProp($partData, 'workerDatas', '');
        
        if (is_array($driverDatas) && sizeof($driverDatas) > 0) {
            $output = getMyProp($driverDatas[0], 'workCompanyId', '');
            return $output;
        }
        if (is_array($workerDatas) && sizeof($workerDatas) > 0) {
            $output = getMyProp($workerDatas[0], 'workCompanyId', '');
            return $output;
        }
        return "";
    }
    
    
    
    public static function getRelateStaffIds($inputDatas) {
        $output = array();

        $partData = getMyProp($inputDatas, 'partCare_', '');
        $driverDatas = getMyProp($partData, 'driverDatas', '');
        $workerDatas = getMyProp($partData, 'workerDatas', '');
        
        if (is_array($driverDatas) && sizeof($driverDatas) > 0) {
            foreach ($driverDatas as $driverData) {
                $staffId = getMyProp($driverData, 'staffId');
                if (!empty($staffId) && !in_array($staffId, $output)) {
                    $output[] = $staffId;
                }
            }
            
        }
        if (is_array($workerDatas) && sizeof($workerDatas) > 0) {
            foreach ($workerDatas as $workerData) {
                $staffId = getMyProp($workerData, 'staffId');
                if (!empty($staffId) && !in_array($staffId, $output)) {
                    $output[] = $staffId;
                }
            }
        }
        
        return $output;
    }
    
    public static function getRelateVehicleType($inputDatas) {
        $partData = getMyProp($inputDatas, 'partRegis_', '');
        $regisDatas = getMyProp($partData, 'regisDatas', '');

        if (is_array($regisDatas) && sizeof($regisDatas) > 0) {
            $output = getMyProp($regisDatas[0], 'vehicleType', '');
            return $output;
        }
        
        return "";
    }
    
}
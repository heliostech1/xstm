<?php

namespace App\Http\Libraries;

use Log;
use App\Http\Models\Rdb;

class PageFactory extends MyBaseLib{


    protected $pages = array();
    private $isInit = false;

    private $menuMain = "appMenuMain";

    private $menuVehicle = "appMenuVehicle";     // A
    private $menuStaff = "appMenuStaff";     // B  
    private $menuCustomer = "appMenuCustomer";  // C
    private $menuBooking = "appMenuBooking";     // D
    private $menuShipment = "appMenuShipment"; // E
    private $menuReport = "appMenuReport"; // F
    private $menuBookVehicle = "appMenuBookVehicle ";  // G    
    private $menuTracking = "appMenuTracking";  // H
    private $menuPayment = "appMenuPayment";   // I  
    private $menuSystem = "appMenuSystem";    // J
    private $menuUserManual = "appMenuUserManual"; // K
    private $menuAlarm = "appMenuAlarm"; // K
    
    public $propertyMainPage = "mainPage";
    public $propertyAccessiblePage = "accessiblePage";

    public function __construct($app) {
    
    }
    
    private function init() {
        if ($this->isInit) return;
        $this->isInit = true;
        //$this->addPage($this->menuMain, "xxxxx", "xxxxx", "xxxxx");

        //$this->addPage($thisaddPage(array(), in, "auth/login", "หน้า login", "....");
        $m = $this->propertyMainPage; // เป็นหน้าหลักที่มีปุ่มกดไปหน้าอื่นๆอีกหลายหน้า
        $a = $this->propertyAccessiblePage; // เป็นหน้าที่เปิดให้ใช้งานได้เสมอ

        /* *********************************************************************
         * SECTION: เมนูหลัก
         * *********************************************************************/
        $this->pResetGroup();
        $this->addPage("", array($a), $this->menuMain, "home", "หน้าแรก", "แสดงรายการหน้าแรกของระบบ");
        $this->addPage("", array($a), $this->menuMain, "logout", "ออกจากระบบ", "");
    

        /* *********************************************************************
         * SECTION: รถขนส่ง
         * *********************************************************************/
          
        $this->pResetGroup();

        $this->addPage("", array($m), $this->menuVehicle, "vehicle/index", "หน้าขึ้นทะเบียนรถขนส่ง", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/add", "หน้าเพิ่มรถขนส่ง", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/edit", "หน้าแก้ไขรถขนส่ง", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/view", "หน้าเรียกดูรถขนส่ง", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/delete", "ลบข้อมูลรถขนส่ง", "");
        

        $this->addPage("", array(), $this->menuVehicle, "vehicle/editRegis", "หน้าแก้ไขรถขนส่ง (ข้อมูลรายการจดทะเบียน)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/viewRegis", "หน้าเรียกดูรถขนส่ง (ข้อมูลรายการจดทะเบียน)", "");

        $this->addPage("", array(), $this->menuVehicle, "vehicle/editOwner", "หน้าแก้ไขรถขนส่ง (ข้อมูลการครอบครองรถ)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/viewOwner", "หน้าเรียกดูรถขนส่ง (ข้อมูลการครอบครองรถ)", "");

        $this->addPage("", array(), $this->menuVehicle, "vehicle/editTax", "หน้าแก้ไขรถขนส่ง (ข้อมูลรายการเสียภาษี)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/viewTax", "หน้าเรียกดูรถขนส่ง (ข้อมูลรายการเสียภาษี)", "");
        
        $this->addPage("", array(), $this->menuVehicle, "vehicle/editContainer", "หน้าแก้ไขรถขนส่ง (ข้อมูลตู้สินค้า)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/viewContainer", "หน้าเรียกดูรถขนส่ง (ข้อมูลตู้สินค้า)", "");
        
        $this->addPage("", array(), $this->menuVehicle, "vehicle/editFuel", "หน้าแก้ไขรถขนส่ง (ข้อมูลเชื้อเพลิง)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/viewFuel", "หน้าเรียกดูรถขนส่ง (ข้อมูลเชื้อเพลิง)", "");
        
        $this->addPage("", array(), $this->menuVehicle, "vehicle/editChiller", "หน้าแก้ไขรถขนส่ง (ข้อมูลเครื่องทำความเย็น)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/viewChiller", "หน้าเรียกดูรถขนส่ง (ข้อมูลเครื่องทำความเย็น)", "");
        
        $this->addPage("", array(), $this->menuVehicle, "vehicle/editCare", "หน้าแก้ไขรถขนส่ง (ข้อมูลการให้บริการ)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/viewCare", "หน้าเรียกดูรถขนส่ง (ข้อมูลการให้บริการ)", "");
        
        $this->addPage("", array(), $this->menuVehicle, "vehicle/editMonitor", "หน้าแก้ไขรถขนส่ง (ข้อมูลการซ่อมบำรุง)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehicle/viewMonitor", "หน้าเรียกดูรถขนส่ง (ข้อมูลการซ่อมบำรุง)", "");
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuVehicle, "vehInsurance/index", "หน้าจัดการข้อมูลประกันภัย", "");
        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/edit", "หน้าแก้ไขประกันภัย", "");
        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/view", "หน้าเรียกดูประกันภัย", "");
      //  $this->addPage("", array(), $this->menuVehicle, "vehInsurance/delete", "ลบข้อมูลประกันภัย", ""); 
        
        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/editInsAct", "หน้าแก้ไขประกันภัย (พรบ)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/viewInsAct", "หน้าเรียกดูประกันภัย (พรบ)", "");

        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/editInsCar", "หน้าแก้ไขประกันภัย (ประกันภัยรถยนต์)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/viewInsCar", "หน้าเรียกดูประกันภัย (ประกันภัยรถยนต์)", "");
        
        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/editInsGoods", "หน้าแก้ไขประกันภัย (ประกันภัยสินค้า)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/viewInsGoods", "หน้าเรียกดูประกันภัย (ประกันภัยสินค้า)", "");
        
        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/editClaim", "หน้าแก้ไขประกันภัย (ประวัติการเคลม)", "");
        $this->addPage("", array(), $this->menuVehicle, "vehInsurance/viewClaim", "หน้าเรียกดูประกันภัย (ประวัติการเคลม)", "");
        
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuVehicle, "vehAccident/index", "หน้าจัดการข้อมูลอุบัติเหตุ-ความเสียหาย", "");
        $this->addPage("", array(), $this->menuVehicle, "vehAccident/add", "หน้าเพิ่มข้อมูลอุบัติเหตุ-ความเสียหาย", "");
        $this->addPage("", array(), $this->menuVehicle, "vehAccident/edit", "หน้าแก้ไขข้อมูลอุบัติเหตุ-ความเสียหาย", "");
        $this->addPage("", array(), $this->menuVehicle, "vehAccident/view", "หน้าเรียกดูข้อมูลอุบัติเหตุ-ความเสียหาย", "");        
        $this->addPage("", array(), $this->menuVehicle, "vehAccident/delete", "ลบข้อมูลข้อมูลอุบัติเหตุ-ความเสียหาย", "");
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuVehicle, "vehRepair/index", "หน้าจัดการข้อมูลการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuVehicle, "vehRepair/add", "หน้าเพิ่มข้อมูลการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuVehicle, "vehRepair/edit", "หน้าแก้ไขข้อมูลการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuVehicle, "vehRepair/view", "หน้าเรียกดูข้อมูลการซ่อมบำรุง", "");        
        $this->addPage("", array(), $this->menuVehicle, "vehRepair/delete", "ลบข้อมูลข้อมูลการซ่อมบำรุง", "");
        
        

        /* *********************************************************************
         * SECTION: พนักงาน
         * *********************************************************************/
          
        $this->pResetGroup();

        $this->addPage("", array($m), $this->menuStaff, "staff/index", "หน้าขึ้นทะเบียนพนักงาน", "");
        $this->addPage("", array(), $this->menuStaff, "staff/add", "หน้าเพิ่มพนักงาน", "");
        $this->addPage("", array(), $this->menuStaff, "staff/edit", "หน้าแก้ไขพนักงาน", "");
        $this->addPage("", array(), $this->menuStaff, "staff/view", "หน้าเรียกดูพนักงาน", "");
        $this->addPage("", array(), $this->menuStaff, "staff/delete", "ลบข้อมูลพนักงาน", "");
        

        $this->addPage("", array(), $this->menuStaff, "staff/editLicense", "หน้าแก้ไขพนักงาน (ข้อมูลใบขับขี่)", "");
        $this->addPage("", array(), $this->menuStaff, "staff/viewLicense", "หน้าเรียกดูพนักงาน (ข้อมูลใบขับขี่)", "");

        $this->addPage("", array(), $this->menuStaff, "staff/editWork", "หน้าแก้ไขพนักงาน (ข้อมูลการทำงาน)", "");
        $this->addPage("", array(), $this->menuStaff, "staff/viewWork", "หน้าเรียกดูพนักงาน (ข้อมูลการทำงาน)", "");

        $this->addPage("", array(), $this->menuStaff, "staff/editAbsent", "หน้าแก้ไขพนักงาน (ข้อมูลวันหยุด/ลา)", "");
        $this->addPage("", array(), $this->menuStaff, "staff/viewAbsent", "หน้าเรียกดูพนักงาน (ข้อมูลวันหยุด/ลา)", "");
        

        /* *********************************************************************
         * SECTION: แจ้งเตือน
         * *********************************************************************/
          
        $this->pResetGroup();
        $this->addPage("",array($m), $this->menuAlarm, "alarmLog/index", "หน้ารายการแจ้งเตือน", "");
        //$this->addPage("",array(), $this->menuAlarm, "alarmLog/view", "View Alarm Log", "");   
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuAlarm, "monitorTopic/index", "หน้าหัวข้อการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuAlarm, "monitorTopic/add", "หน้าเพิ่มหัวข้อการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuAlarm, "monitorTopic/edit", "หน้าแก้ไขหัวข้อการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuAlarm, "monitorTopic/view", "หน้าเรียกดูหัวข้อการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuAlarm, "monitorTopic/delete", "ลบข้อมูลหัวข้อการซ่อมบำรุง", "");
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuAlarm, "monitorPlan/index", "หน้าแผนการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuAlarm, "monitorPlan/add", "หน้าเพิ่มแผนการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuAlarm, "monitorPlan/edit", "หน้าแก้ไขแผนการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuAlarm, "monitorPlan/view", "หน้าเรียกดูแผนการซ่อมบำรุง", "");
        $this->addPage("", array(), $this->menuAlarm, "monitorPlan/delete", "ลบข้อมูลแผนการซ่อมบำรุง", "");
        
        
        
        /* *********************************************************************
         * SECTION: จัดการข้อมูลระบบ
         * *********************************************************************/

        
        $this->pResetGroup();
        $this->addPage("", array($m), $this->menuSystem, "account/index", "หน้าบัญชี", "");
        $this->addPage("", array(), $this->menuSystem, "account/add", "หน้าเพิ่มบัญชี", "");
        $this->addPage("", array(), $this->menuSystem, "account/edit", "หน้าแก้ไขกบัญชี", "");
        $this->addPage("", array(), $this->menuSystem, "account/view", "หน้าเรียกดูบัญชี", "");        
        $this->addPage("", array(), $this->menuSystem, "account/delete", "ลบบัญชี", "");

        
        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "userGroup/index", "หน้ากลุ่มผู้ใช้ ", "");
        $this->addPage("", array(), $this->menuSystem, "userGroup/add", "หน้าเพิ่มกลุ่มผู้ใช้ ", "");
        $this->addPage("", array(), $this->menuSystem, "userGroup/edit", "หน้าแก้ไขกลุ่มผู้ใช้ ", "");
        $this->addPage("", array(), $this->menuSystem, "userGroup/delete", "ลบกลุ่มผู้ใช้ ", "");
        $this->addPage("", array(), $this->menuSystem, "userGroup/editPagePermission", "หน้าตั้งค่าสิทธิ์การใช้หน้าต่างๆ ", "");
        $this->addPage("", array(), $this->menuSystem, "userGroup/viewPagePermission", "หน้าเรียกดูสิทธิ์การใช้หน้าต่างๆ ", "");


        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "user/index", "หน้าผู้ใช้ ", "");
        $this->addPage("", array(), $this->menuSystem, "user/add", "หน้าเพิ่มผู้ใช้", "");
        $this->addPage("", array(), $this->menuSystem, "user/edit", "หน้าแก้ไขผู้ใช้", "");
        $this->addPage("", array(), $this->menuSystem, "user/view", "หน้าเรียกดูผู้ใช้", "");
        $this->addPage("", array(), $this->menuSystem, "user/delete", "ลบข้อมูลผู้ใช้", "");
        $this->addPage("", array(), $this->menuSystem, "user/viewSiteUsageHistory", "หน้าเรียกดูประวัติการเข้าใช้ระบบ ", "");
    
        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "user/changePassword", "หน้าเปลี่ยนรหัสผ่าน", "");

        $this->pReset();          
        $this->addPage("",array($m), $this->menuSystem, "appSetting/index", "หน้าตั้งค่าใช้งานระบบ", "");
        $this->addPage("",array(), $this->menuSystem, "appSetting/edit", "หน้าแก้ไขการตั้งค่าใช้งานระบบ", "");
        $this->addPage("",array(), $this->menuSystem, "appSetting/view", "หน้าเรียกดูการตั้งค่าใช้งานระบบ", "");
        
        $this->pReset();          
        $this->addPage("",array($m), $this->menuSystem, "appSettingForUser/index", "หน้าตั้งค่าใช้งานระบบสำหรับผู้ใช้", "");
        $this->addPage("",array(), $this->menuSystem, "appSettingForUser/edit", "หน้าแก้ไขการตั้งค่าใช้งานระบบสำหรับผู้ใช้", "");
        $this->addPage("",array(), $this->menuSystem, "appSettingForUser/view", "หน้าเรียกดูการตั้งค่าใช้งานระบบสำหรับผู้ใช้", "");
        
        
        $this->pReset();
        $this->addPage("",array($m), $this->menuSystem, "alarmSetting/index", "หน้าตั้งค่างานแจ้งเตือน", "");
        $this->addPage("",array(), $this->menuSystem, "alarmSetting/edit", "หน้าแก้ไขการตั้งค่างานแจ้งเตือน", "");          
        $this->addPage("",array(), $this->menuSystem, "alarmSetting/view", "หน้าเรียกดูการตั้งค่างานแจ้งเตือน", "");     
        
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "goodsContainer/index", "หน้าชนิดตู้สินค้า", "");
        $this->addPage("", array(), $this->menuSystem, "goodsContainer/add", "หน้าเพิ่มชนิดตู้สินค้า", "");
        $this->addPage("", array(), $this->menuSystem, "goodsContainer/edit", "หน้าแก้ไขชนิดตู้สินค้า", "");
        $this->addPage("", array(), $this->menuSystem, "goodsContainer/view", "หน้าเรียกดูชนิดตู้สินค้า", "");
        $this->addPage("", array(), $this->menuSystem, "goodsContainer/delete", "ลบข้อมูลชนิดตู้สินค้า", "");
        
                
        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "refrigerant/index", "หน้าชนิดสารทำความเย็น", "");
        $this->addPage("", array(), $this->menuSystem, "refrigerant/add", "หน้าเพิ่มชนิดสารทำความเย็น", "");
        $this->addPage("", array(), $this->menuSystem, "refrigerant/edit", "หน้าแก้ไขชนิดสารทำความเย็น", "");
        $this->addPage("", array(), $this->menuSystem, "refrigerant/view", "หน้าเรียกดูชนิดสารทำความเย็น", "");
        $this->addPage("", array(), $this->menuSystem, "refrigerant/delete", "ลบข้อมูลชนิดสารทำความเย็น", "");
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "vehicleCare/index", "หน้าผู้ให้บริการ", "");
        $this->addPage("", array(), $this->menuSystem, "vehicleCare/add", "หน้าเพิ่มผู้ให้บริการ", "");
        $this->addPage("", array(), $this->menuSystem, "vehicleCare/edit", "หน้าแก้ไขผู้ให้บริการ", "");
        $this->addPage("", array(), $this->menuSystem, "vehicleCare/view", "หน้าเรียกดูผู้ให้บริการ", "");
        $this->addPage("", array(), $this->menuSystem, "vehicleCare/delete", "ลบข้อมูลผู้ให้บริการ", "");
        
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "vCareType/index", "หน้าชนิดรถให้บริการ", "");
        $this->addPage("", array(), $this->menuSystem, "vCareType/add", "หน้าเพิ่มชนิดรถให้บริการ", "");
        $this->addPage("", array(), $this->menuSystem, "vCareType/edit", "หน้าแก้ไขชนิดรถให้บริการ", "");
        $this->addPage("", array(), $this->menuSystem, "vCareType/view", "หน้าเรียกดูชนิดรถให้บริการ", "");
        $this->addPage("", array(), $this->menuSystem, "vCareType/delete", "ลบข้อมูลชนิดรถให้บริการ", "");
        
        /*
        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "gasTankSetting/index", "หน้าตั้งค่าข้อมูลถังแก๊ส", "");
        $this->addPage("", array(), $this->menuSystem, "gasTankSetting/edit", "หน้าแก้ไขตั้งค่าข้อมูลถังแก๊ส", "");
        $this->addPage("", array(), $this->menuSystem, "gasTankSetting/view", "หน้าเรียกดูตั้งค่าข้อมูลถังแก๊ส", "");
        */
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "workCompany/index", "หน้าสังกัด", "");
        $this->addPage("", array(), $this->menuSystem, "workCompany/add", "หน้าเพิ่มสังกัด", "");
        $this->addPage("", array(), $this->menuSystem, "workCompany/edit", "หน้าแก้ไขสังกัด", "");
        $this->addPage("", array(), $this->menuSystem, "workCompany/view", "หน้าเรียกดูสังกัด", "");
        $this->addPage("", array(), $this->menuSystem, "workCompany/delete", "ลบข้อมูลสังกัด", "");
        
        $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "staffType/index", "หน้าชนิดพนักงาน", "");
        $this->addPage("", array(), $this->menuSystem, "staffType/add", "หน้าเพิ่มชนิดพนักงาน", "");
        $this->addPage("", array(), $this->menuSystem, "staffType/edit", "หน้าแก้ไขชนิดพนักงาน", "");
        $this->addPage("", array(), $this->menuSystem, "staffType/view", "หน้าเรียกดูชนิดพนักงาน", "");
        $this->addPage("", array(), $this->menuSystem, "staffType/delete", "ลบข้อมูลชนิดพนักงาน", "");
        
         $this->pReset();
        $this->addPage("", array($m), $this->menuSystem, "licenseType/index", "หน้าประเภทใบขับขี่", "");
        $this->addPage("", array(), $this->menuSystem, "licenseType/add", "หน้าเพิ่มประเภทใบขับขี่", "");
        $this->addPage("", array(), $this->menuSystem, "licenseType/edit", "หน้าแก้ไขประเภทใบขับขี่", "");
        $this->addPage("", array(), $this->menuSystem, "licenseType/view", "หน้าเรียกดูประเภทใบขับขี่", "");
        $this->addPage("", array(), $this->menuSystem, "licenseType/delete", "ลบข้อมูลประเภทใบขับขี่", "");
        
        
        /* *********************************************************************
         * SECTION: คู่มือการใช้งาน
         * *********************************************************************/
     
        $this->pResetGroup();       
        $this->addPage("",array($m), $this->menuUserManual, "userManual/index", "หน้าคู่มือการใช้งาน", "");
        $this->addPage("",array(), $this->menuUserManual, "userManual/edit", "หน้าแก้ไขคู่มือการใช้งาน", "");
        
                
    }
    
    //============================================================
    // auto page prefix  generator

    private $p_first = 0;
    private $p_second = 0;
    private $p_displays = array("0", "1","2","3","4","5","6","7","8","9","A","B","C",
            "D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");

    private function pResetGroup() {
        $this->p_first = 1;
        $this->p_second = 0;
    }

    private function pReset() {
        $this->p_first++;
        $this->p_second = 0;
    }

    private function pAuto() {
        $page_no = $this->p_displays[ $this->p_first ].$this->p_displays[ $this->p_second ];
        $this->p_second++;
        return $page_no;
    }


    //======================================

    public function getMenuDesc($name) {
        if ($name == $this->menuMain)  return "เมนูหลัก";   
        
        if ($name == $this->menuVehicle)  return "จัดการรถขนส่ง";   
        if ($name == $this->menuStaff)  return "จัดการพนักงาน";           
        if ($name == $this->menuCustomer) return "จัดการลูกค้า";           
        if ($name == $this->menuBooking) return "จัดการการจองรถขนส่งสินค้า";             
        if ($name == $this->menuShipment) return "จัดการการขนส่งสินค้า";
        
        if ($name == $this->menuReport) return "รายงาน";           
        if ($name == $this->menuBookVehicle) return "จองรถขนส่ง";    
        if ($name == $this->menuTracking) return "ติดตามการปฏิบัติงาน";             
        if ($name == $this->menuPayment) return "ข้อมูลการวางบิลและชำระเงิน";
        if ($name == $this->menuSystem) return "จัดการข้อมูลระบบ";     
        
        if ($name == $this->menuUserManual) return "คู่มือการใช้งาน";          
        if ($name == $this->menuAlarm) return "แจ้งเตือน";   

    }

    public function getMenuNo($name) {
        $i = 1; if ($name == $this->menuMain)  return $i;
        $i++; if ($name == $this->menuVehicle) return $i;   
        $i++; if ($name == $this->menuStaff) return $i;   
        $i++; if ($name == $this->menuAlarm) return $i;  
        
        $i++; if ($name == $this->menuSystem) return "I";  
        $i++; if ($name == $this->menuUserManual) return "II"; 
        return 0;
    }

    private function addPage($page_no, $property, $menu, $page_id, $page_name, $page_desc, $page_modes = array()) {
        $page_no = (empty($page_no))? $this->pAuto(): $page_no;

        $page = new \stdClass();
        $page->menu = $menu;
        $page->id = $page_id;
        $page->name = $page_name;
        $page->description = $page_desc;
        $page->property = $property;
        $page->mode_opt = $page_modes;
        $page->for_plan = ""; // $for_plan;
        
        $menu_no = $this->getMenuNo($menu);
        $page->page_no = $menu_no."-".$page_no;

        $this->pages[] = $page;
    }

    public function getPage($page_id) {
        $this->init();

        foreach ($this->pages as $page) {
            if ($page->id == $page_id) {
                return $page;
            }
        }

        //echo "ERROR: Not found '$page_id' in Page_factory";
        return false;
    }

    public function getPageName($page_id) {
        $page = $this->getPage($page_id);
        return ($page)? $page->name: $page_id;
    }

    public function getPageNo($page_id) {
        $page = $this->getPage($page_id);
        return ($page)? $page->page_no: "";
    }

    public function getPageDatas($appPlanId="") {
        $this->init();

        $pages = array();
        foreach ($this->pages as $page) {
           // if ($this->isPageForPlan($page, $appPlanId)) {
                $pages[] = $page;
           // }            
        }

        return $pages;
    }
    
    public function isPageForPlan($page, $appPlanId) {
        return true; 
        /* no plan for this app
        $forPlan = $page->for_plan;
        
        $planNo = ($appPlanId == Rdb::$APP_PLAN_STARTUP)? 1: 0;
        $planNo = ($appPlanId == Rdb::$APP_PLAN_BASIC)? 2: $planNo;
        $planNo = ($appPlanId == Rdb::$APP_PLAN_ADVANCE)? 3: $planNo;
        
        return  (in_array($planNo, $forPlan))? true: false;          
         */
    }

    public function isAccessiblePage($page_id) {
        $page = $this->getPage($page_id);
        if ($page && in_array($this->propertyAccessiblePage, $page->property)) {
            return true;
        }
        return false;
    }



}



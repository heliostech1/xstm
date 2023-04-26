<?php

namespace App\Http\Libraries;

use Log;
use App\Http\Models\User;
use App\Http\Models\UserGroup;
use App\Http\Models\Branch;
use App\Http\Models\Rdb;
use App\Http\Models\Product\Category;
use App\Http\Models\Product\Unit;
use App\Http\Models\Account;
use App\Http\Models\Common\Staff;
use App\Http\Models\Common\Driver;
use App\Http\Models\Vehicle\Vehicle;


class DropdownMgr extends MyBaseLib
{        
    public $app;
    public $request;
    
    public static $textChoose = '-- กรุณาเลือก --';
    private static $textAll = '-- ทั้งหมด --';
    private static $textChooseShort = '-กรุณาเลือก-';
    
    public  function __construct()
    {  
        //Log::debug("AuthMgr construct");
        //$this->app = $app;
        //$this->request = $this->app['request'];
    }
    
    //========================================================
    //
    // CONSTANT DROPDOWNS
    //
    //========================================================
    
    public static function getEmptyArray() {
        return static::_toOptionArray(array());
    }
    
    public static function getActiveArray($empty_opt=true) {
        return self::_toOptionArray(Rdb::getActive(), null, $empty_opt);
    }
    
    public static function getUseUnuseArray($empty_opt = false, $use_txt = "", $unuse_txt = "") {
        $option = array();
        if ($empty_opt) {
            $option[''] = self::$textChoose;
        }
    
        $option['1'] = (empty($use_txt))? "ใช้" : $use_txt;
        $option['-1'] = (empty($unuse_txt))? "ไม่ใช้" : $unuse_txt;
        return $option;
    }
    
    
    public static function getYesNoArray($empty_opt=true, $yes_txt="ใช่", $no_txt="ไม่ใช่") {
        $option = array();
        if ($empty_opt) {
            $option[''] = self::$textChoose;
        }
        $option[Rdb::$YES] = $yes_txt;
        $option[Rdb::$NO] = $no_txt;
        return $option;
    }
   
    public static function getNoYesArray($empty_opt=true, $yes_txt="ใช่", $no_txt="ไม่ใช่") {
        $option = array();
        if ($empty_opt) {
            $option[''] = self::$textChoose;
        }
        $option[Rdb::$NO] = $no_txt;        
        $option[Rdb::$YES] = $yes_txt;
        return $option;
    }
    
    public static function getHourAmountArray() {
        $option = array(
              "0" => "0","1" => "1","2" => "2","3" => "3","4" => "4","5" => "5","6" => "6","7" => "7","8" => "8","9" => "9"
        );
        return $option;
    }
    
    
   public  static function getYearArray() {
        $curr_year = DateHelper::todayThaiYear();
        $option = array();
        for ($i = 0; $i < 20; $i++) {
            $year = $curr_year-$i;
            $option["$year"] =  "$year";
        }
        return $option;
    }
    
    public static function getMonthArray() {
        $option = array( "" => self::$textChoose_short,
                "1" => "มกราคม","2" => "กุมภาพันธ์","3" => "มีนาคม","4" => "เมษายน","5" => "พฤษภาคม","6" => "มิถุนายน",
                "7" => "กรกฎาคม","8" => "สิงหาคม","9" => "กันยายน","10" => "ตุลาคม","11" => "พฤศจิกายน","12" => "ธันวาคม",
        );
        return $option;
    }
    
    public static function getDayArray() {
        $option = array();
        $option[""] = self::$textChoose_short;
        for ($i = 1; $i <= 31; $i++) {
            $option["$i"] =  "$i";
        }
        return $option;
    }
    
    public static function getUpdateIntervalArray() {
        $option = array();
        //$option["5"] = "5 วินาที"; // for test
        //$option["7"] = "7 วินาที"; // for test
        $option["300"] = "5 นาที";
        $option["420"] = "7 นาที";
        $option["600"] = "10 นาที";
        return $option;
    }
     
    
    public static function getCompareSignArray() {
        $option = array();
        $option[''] = self::$textChoose;
        $option["&gt;"] = "&gt;";
        $option["&gt;="] = "&gt;=";
        $option["&lt;"] = "&lt;";
        $option["&lt;="] = "&lt;=";
        $option["="] = "=";
        return $option;
    }
    
    
    public static function getCompareOperationArray() {
        $option = array();
        $option["="] = "=";
        $option["<"] = "<";
        $option[">"] = ">";
        return $option;
    }
    
    public static function getTextChoose() {
        return self::$textChoose;
    }
    
    //==========================================================================
    //
    // DROPDOWN BY RDB
    //
    //==========================================================================
    
    
    static function getAppPlanArray() {
        return self::_toOptionArray(Rdb::getAppPlan() );
    }
        

    public static function getFuelArray() {
        return self::_toOptionArray(Rdb::getFuel() );
    }
    
    public static function getFuelOilArray() {
        return self::_toOptionArray(Rdb::getFuelOil() );
    }
    
    public static function getFuelGasArray() {
        return self::_toOptionArray(Rdb::getFuelGas() );
    }
    
    public static function getClaimTypeArray() {
        return self::_toOptionArray(Rdb::getClaimType() );
    }

    public static function getWorkStatusArray() {
        return self::_toOptionArray(Rdb::getWorkStatus() );
    }
    
    public static function getMonitorDataTypeArray() {
        return self::_toOptionArray(Rdb::getMonitorDataType() );
    }
    
    public static function getRepairGroupArray() {
        return self::_toOptionArray(Rdb::getRepairGroup() );
    }
        

    
    
    //==========================================================================
    //
    // MODEL DROPDOWNS
    //
    //==========================================================================

    
    static function getAccountArray() {
        $results = Account::getAllData();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            $option[$row['accountId']] = $row['accountId'];
        }
    
        return $option;
    }
    
    
    public static function getUserArray() {
        $results = User::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            $option[$row['userId']] = $row['userId'];
        }
    
        return $option;
    }
    
    public static function getUserGroupArray() {
        $results = UserGroup::getUserGroupList();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            $option[$row['userGroupId']] = $row['name'];
        }
    
        return $option;
    }


    
    public static function getBranchArray() {
        $results = Branch::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            if (isset($row['name'])) {
                $mongoId = MongoHelper::getIdByObject($row['_id']);
                $option[$mongoId] = $row['name'];
            }
        }
    
        return $option;
    }
    

    
    public static function getCategoryArray() {
        $results = Category::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            if (isset($row['name'])) {
                $mongoId = MongoHelper::getIdByObject($row['_id']);
                $option[$mongoId] = $row['name'];
            }
        }
    
        return $option;
    }

    
    
    public static function getUnitArray() {
        $results = Unit::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            if (isset($row['name'])) {
                $name = $row['name'];
                //$mongoId = MongoHelper::getIdByObject($row['_id']);
                $option[$name] = $name;
            }
        }
    
        return $option;
    }
    

    public static function getStaffArray() {
        $results = Staff::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            if (isset($row['staffId']) && isset($row['name']) ) {
                $mongoId = $row['staffId'];
                $option[$mongoId] = $row['name'] . ' ' .getMyProp($row, 'surname','');
            }
        }
    
        return $option;
    }    
    
    public static function getDriverArray() {
        $results = Driver::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            if (isset($row['driverId']) && isset($row['name']) ) {
                $mongoId = $row['driverId'];
                $option[$mongoId] = $row['name']. ' ' . getMyProp($row, 'surname','');
            }
        }
    
        return $option;
    }    
    
    
    public static function getVehicleArray() {
        $results = Vehicle::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            $licensePlate = getMyProp($row, 'licensePlate', '');
            $vehicleId = getMyProp($row, 'vehicleId', '');            
            $option[$vehicleId] = $licensePlate;     
        }
    
        return $option;
    }  
    
    public static function getLicensePlateArray() {
        $results = Vehicle::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            $licensePlate = getMyProp($row, 'licensePlate', '');          
            $option[$licensePlate] = $licensePlate;     
        }
    
        return $option;
    }  
    
    public static function getLicensePlateList() {
        $results = Vehicle::getAllDataArray();
        $option = array();
    
        foreach ($results as $row) {
            $licensePlate = getMyProp($row, 'licensePlate', '');          
            $option[] = $licensePlate;     
        }
    
        return $option;
    }  
    
    public static function getMonitorTopicArray() {
        $results = \App\Http\Models\Alarm\MonitorTopic::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            if (isset($row['name'])) {
                //$mongoId = MongoHelper::getIdByObject($row['_id']);
                $name = $row['name'];
                $option[$name] = $name;
            }
        }
    
        return $option;
    }
    
    public static function getMonitorPlanArray() {
        $results = \App\Http\Models\Alarm\MonitorPlan::getAllDataArray();
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            if (isset($row['name'])) {
                $mongoId = MongoHelper::getIdByObject($row['_id']);
                $option[$mongoId] = $row['name'];
            }
        }
    
        return $option;
    }    

    
    //----------------------------------------------

    public static function getRefrigerantArray() {
        return self::getCommonRdbArray(  \App\Http\Models\Common\Refrigerant::getAllDataArray(true) );
    }   
    
    public static function getGoodsContainerArray() {
        return self::getCommonRdbArray(  \App\Http\Models\Common\GoodsContainer::getAllDataArray(true) );
    }  
    
    public static function getVehicleCareArray() {
        return self::getCommonRdbArray(  \App\Http\Models\Common\VehicleCare::getAllDataArray(true) );
    }  
    
    public static function getVCareTypeArray() {
        return self::getCommonRdbArray(  \App\Http\Models\Common\VCareType::getAllDataArray(true) );
    }  
    
    public static function getWorkCompanyArray() {
        return self::getCommonRdbArray( \App\Http\Models\Common\WorkCompany::getAllDataArray(true) );
    }  
    
    public static function getStaffTypeArray() {
        return self::getCommonRdbArray( \App\Http\Models\Common\StaffType::getAllDataArray(true) );
    }  
    
    public static function getLicenseTypeArray() {
        return self::getCommonRdbArray( \App\Http\Models\Common\LicenseType::getAllDataArray(true) );
    }  
    
    
    public static function getCommonRdbArray($results) {
        $option = array();
        $option[''] = self::$textChoose;
    
        foreach ($results as $row) {
            if (isset($row['name'])) {
                $mongoId = MongoHelper::getIdByObject($row['_id']);
                $active = (getMyProp($row, 'active', ''));
                
                $option[$mongoId] = array(
                    "text" => $row['name'], 
                    "active" => !empty($active)? $active: Rdb::$YES
                );
            }
        }
    
        return $option;
    }  
    
    //===================================================================
    
    
    static function _toOptionArray($results, $addition_opt = null, $incTextChoose = true) {
        $option = array();
    
        if ($incTextChoose) {
            $option[''] = self::$textChoose;
        }
    
        if (!empty($addition_opt)) {
            $option = array_merge($option, $addition_opt);
        }
    
        foreach ($results as $row) {
            $option[$row['name']] = $row['description'];
        }
    
        return $option;
    }
    
}

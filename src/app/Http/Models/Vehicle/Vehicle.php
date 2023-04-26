<?php

namespace App\Http\Models\Vehicle;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Core\MongoTable;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Models\Core\MongoCounter;
use App\Http\Models\Common\VCareType;
use App\Http\Models\Common\GoodsContainer;
use App\Http\Models\Common\WorkCompany;
use App\Http\Models\Staff\Staff;
use App\Http\Models\Alarm\MonitorPlan;

class Vehicle extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'vehicle';
    
    protected $primaryKey = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    private static $allDataArrayForView;
    private static $allDataInSystem;
    
    public static function getDataTable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        $error = "";        
            

        $where = MongoHelper::appendWhere($where, 'accountId', self::getLoginAccountId() );
        $totalWhere = $where;
        
        $where = MongoHelper::appendWhere($where, 'vehicleId', getMyProp( $criDatas, 'vehicleId') );
        $where = MongoHelper::appendWhere($where, 'active', getMyProp( $criDatas, 'active') );
        
        //---------------------
        $where = MongoHelper::appendWhere($where, 'licensePlate', getMyProp( $criDatas, 'licensePlate') );
        $where = MongoHelper::appendWhere($where, 'partRegis_.regisDatas.brand', getMyProp( $criDatas, 'brand') );
        
        if (self::checkValidThaiDate( getMyProp( $criDatas, 'regisDateFrom'), "วันจดทะเบียน (จาก)") &&
            self::checkValidThaiDate( getMyProp( $criDatas, 'regisDateTo'), "วันจดทะเบียน (ถึง)")
        ) {
            $where = MongoHelper::appendWhereDateRange($where, 'partRegis_.regisDatas.regisDate',  
                    getMyProp( $criDatas, 'regisDateFrom'), getMyProp( $criDatas, 'regisDateTo'), false);
        }
        //$where = MongoHelper::appendWhere($where, 'partRegis_.regisDatas.fuel', getMyProp( $criDatas, 'fuel') );
        $where = MongoHelper::appendWhere($where, 'partOwner_.ownerDatas.ownerName', getMyProp( $criDatas, 'ownerName') );
        $where = MongoHelper::appendWhere($where, 'partContainer_.containerType', getMyProp( $criDatas, 'containerType') );
        
        //----------------------
        
        $where = MongoHelper::appendWhere($where, 'partCare_.vCareType', getMyProp( $criDatas, 'vCareType') );        
        $where = MongoHelper::appendWhere($where, 'partFuel_.oilType', getMyProp( $criDatas, 'oilType') );
        $where = MongoHelper::appendWhere($where, 'partFuel_.gasType', getMyProp( $criDatas, 'gasType') );
        $where = MongoHelper::appendWhere($where, 'partCare_.vehicleCare', getMyProp( $criDatas, 'vehicleCare') );
        $where = MongoHelper::appendWhere($where, 'partRegis_.regisDatas.bodyNumber', getMyProp( $criDatas, 'bodyNumber') );
        
        //----------------------
        
        $where = MongoHelper::appendWhere($where, 'partRegis_.regisDatas.engineNumber', getMyProp( $criDatas, 'engineNumber') );
        
        if (self::checkValidThaiDate(getMyProp( $criDatas, 'taxDueDateFrom') , "วันภาษีรถ (จาก)") &&
            self::checkValidThaiDate(getMyProp( $criDatas, 'taxDueDateTo') , "วันภาษีรถ (ถึง)")
        ) {
            $where = MongoHelper::appendWhereDateRange($where, 'partTax_.taxDatas.dueDate',
                    getMyProp( $criDatas, 'taxDueDateFrom'), getMyProp( $criDatas, 'taxDueDateTo'), false);                    
        }
        
        if (self::checkValidThaiDate(getMyProp( $criDatas, 'gasExpDateFrom') , "วันหมดอายุถังแก๊ส (จาก)") &&
            self::checkValidThaiDate(getMyProp( $criDatas, 'gasExpDateTo') , "วันหมดอายุถังแก๊ส (ถึง)")
        ) {
            $where = MongoHelper::appendWhereDateRange($where, 'partFuel_.gasDatas.expDate',
                    getMyProp( $criDatas, 'gasExpDateFrom'), getMyProp( $criDatas, 'gasExpDateTo'), false);                    
        }
        
        
        //=============================================================================
        // INSURANCE FILTER
        
        $where = MongoHelper::appendWhere($where, 'partInsAct_.insActDatas.company', getMyProp( $criDatas, 'actCompany') );
        $where = MongoHelper::appendWhere($where, 'partInsCar_.insCarDatas.company', getMyProp( $criDatas, 'carCompany') );
        $where = MongoHelper::appendWhere($where, 'partInsGoods_.insGoodsDatas.company', getMyProp( $criDatas, 'goodsCompany') );
        
        
        
        //=============================================================================
        
        
        $columns = array( '_id', 'vehicleId', 'description', 'active', 'createdAt', 'workCompany',
            'licensePlate' ,'province', 'odometer', 'partCare_', 'partContainer_', 'partMonitor_');
         
        $output = MongoTable::getOutput(
             self::$TABLE_NAME, $columns , array( "where" => $where, "totalWhere" => $totalWhere)
        );
        
        foreach ($output["aaData"] as &$row)
        {
            $row['mongoId'] =  MongoHelper::getIdByObject($row['_id']);
            $row['vehicleId'] = (!empty($row['vehicleId']))? $row['vehicleId']: "";
            $row['licensePlate'] = getMyProp($row, 'licensePlate', '');
            $row['province'] = getMyProp($row, 'province', '');            
            $row['active'] =  (!empty($row['active']))? Rdb::getActive($row['active']): "";
            $row['createdAt'] = (!empty($row['createdAt']))? DateHelper::mongoDateToThai($row['createdAt']):"";            
            $row['odometer'] = getMyProp($row, 'odometer', '');      
            
            $row['workCompany'] =  WorkCompany::getDataName( getMyProp($row, 'workCompany', '') );           
            $row['vCareType'] = self::getRowItemVCareType($row);
            $row['containerType'] = self::getRowItemContainerType($row);
            $row['monitorPlan'] = self::getRowItemMonitorPlan($row);            
            
        }
        
        $output["message"] = self::errors();
        
        return $output;
    }
    
    public static function getRowItemVCareType($row) {
       $partData = getMyProp($row, 'partCare_', '');  
       $output = getMyProp($partData, 'vCareType', '');  
       return VCareType::getDataName( $output );
    }
    
    public static function getRowItemContainerType($row) {
       $partData = getMyProp($row, 'partContainer_', '');  
       $output = getMyProp($partData, 'containerType', '');  
       return GoodsContainer::getDataName( $output );
    }
    
    public static function getRowItemMonitorPlan($row) {
       $partData = getMyProp($row, 'partMonitor_', '');  
       $output = getMyProp($partData, 'monitorPlan', '');  
       return MonitorPlan::getDataName( $output );
    }
    
    
    public static function getData($keyId, $onlyActive = false) {               
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
    
        $query = DB::table(self::$TABLE_NAME);
        $query->where("_id", $keyId);
    
        if ($onlyActive) {
            $query->where('active', Rdb::$YES);
        }
        $result = $query->first();
        if (!empty($result)) {
            $result['mongoId'] = MongoHelper::getIdByObject($result['_id']);        
        }
            
        return $result;
    }

    public static function getDataByVehicleId($keyId) { 
        $keyId = DataHelper::toInteger($keyId);
        if (empty($keyId)) return false;

        $query = DB::table(self::$TABLE_NAME);
        $query->where("vehicleId", $keyId);
   
        $result = $query->first();
        if (!empty($result)) {
            $result['mongoId'] = MongoHelper::getIdByObject($result['_id']);        
        }
            
        return $result;
    }
    
    public static function getDataByLicensePlate($keyId) { 
        if (empty($keyId)) return false;

        $query = DB::table(self::$TABLE_NAME);
        $query->where("licensePlate", $keyId);
   
        $result = $query->first();
        if (!empty($result)) {
            $result['mongoId'] = MongoHelper::getIdByObject($result['_id']);        
        }
            
        return $result;
    }
    
    public static function getLicensePlateByVehicleId($keyId) {                           
        $data = self::getDataByVehicleId($keyId);
       // myDebug($data);
        return getMyProp($data, 'licensePlate', '');
    }    
    
    public static function getVehicleIdByLicensePlate($keyId) {                           
        $data = self::getDataByLicensePlate($keyId);

        return getMyProp($data, 'vehicleId', '');
    }    

    
    public static function getAllDataArray() {
        if (is_null(self::$allDataArray)) {
            $query = DB::table(self::$TABLE_NAME);
            $query->where('active', Rdb::$YES);
            $query->where('accountId', self::getLoginAccountId());            
            $query->orderBy("vehicleId", "asc");
            self::$allDataArray =  $query->get();
        }
    
        return self::$allDataArray;
    }    

    public static function getAllDataArrayForView() {
        if (is_null(self::$allDataArrayForView)) {

            $query = DB::table(self::$TABLE_NAME);
            $query->where('accountId', self::getLoginAccountId());            
            $query->orderBy("vehicleId", "asc");
            self::$allDataArrayForView =  $query->get();
        }
    
        return self::$allDataArrayForView;
    }    
    
    public static function getAllDataInSystem() {
        if (is_null(self::$allDataInSystem)) {
            $query = DB::table(self::$TABLE_NAME);
            $query->where('active', Rdb::$YES);      
            $query->orderBy("vehicleId", "asc");
            self::$allDataInSystem =  $query->get();
        }
    
        return self::$allDataInSystem;
    }    

    
    public static function getLicensePlateByVehicleIdByCache($inputValue) {
        $datas = self::getAllDataArrayForView();
        
        foreach ($datas as $data) {
            if (getMyProp($data, 'vehicleId') == $inputValue) {
                return getMyProp($data, 'licensePlate', '');
            }
        }
    
        return $inputValue;
    }

    //======================================================================
    
    public static function addData($data)
    {
        $data['accountId'] = self::getLoginAccountId();
        $data['vehicleId'] = MongoCounter::getNextSequence(self::$TABLE_NAME); // TYPE INT
        $data['createdAt'] = MongoHelper::date();
        $data['active'] = Rdb::$YES;        
       // $data['fileDatas'] = self::prepareFileDatasForSave( $data['fileDatas']); 

                
        $result = DB::table(self::$TABLE_NAME)->insertGetId($data);   
        
        Staff::updateRelateVehicle($data);
        
        return true;
    }   
    
    
    public static function editData($keyId, $data, $vehicleId) {
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($objectId)) return false;
        
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($data);

        $data['vehicleId'] = $vehicleId;
        Staff::updateRelateVehicle($data);  
        
        return true;
    }   
    
    public static function editSimpleDataByVehicleId($vehicleId, $data) {           
        if (empty($vehicleId)) return false;
        $vehicleId = DataHelper::toInteger($vehicleId);
        
        DB::table(self::$TABLE_NAME)->where('vehicleId', $vehicleId )->update($data);
        
        return true;
    } 
    
    public static function deleteData($keyId) {
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
        
        DB::table(self::$TABLE_NAME)->where('_id', $keyId )->delete();
        return true;
    }    
     
    
    
    //======================================================================
    
    /*
    public static function prepareFileDatasForGet($output, $format=true) {
        $rets = array();
        $datas = (isset($output['fileDatas']))? $output['fileDatas']: array();
    
        foreach ($datas as $data) {
            $rets[]= array(
                    "fileName" => (isset($data['fileName']))? $data['fileName']: "",
                    "caption" => (isset($data['caption']))? $data['caption']: "",
                    "tags" => (isset($data['tags']))? $data['tags']: "",
            );
        }
        return $rets;
    }

    
    
    public static function prepareFileDatasForSave($datas) {
        $rets = array();
        if (empty($datas)) return $rets;
    
        //DataHelper::debug($datas);
        
        foreach ($datas as $data) {
            $rets[] = array(
                    "fileName" => getMyProp( $data , 'fileName'),
                    "caption" => getMyProp( $data , 'caption', ''), 
                    "tags" => getMyProp( $data , 'tags', ''), 
            );
        }
        return $rets;
    }
    
    */
    
}




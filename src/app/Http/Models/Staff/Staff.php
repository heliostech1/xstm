<?php

namespace App\Http\Models\Staff;

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
use App\Http\Models\Vehicle\Vehicle;
use App\Http\Models\Common\WorkCompany;
use App\Http\Libraries\BigPage\StaffHelper;
use App\Http\Libraries\BigPage\VehicleHelper;

class Staff extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'staff';
    
    protected $primaryKey = '_id';
    
    public $incrementing = false;
    public $timestamps = false;    
    
    private static $allDataArray;
    
    public static function getDataTable($request, $criDatas) {
        $where = array();
        $totalWhere = array();
        $message = "";
        $error = "";        
            

        $where = MongoHelper::appendWhere($where, 'accountId', self::getLoginAccountId() );
        $totalWhere = $where;
        
        $where = MongoHelper::appendWhere($where, 'staffCode', getMyProp( $criDatas, 'staffCode') );
        $where = MongoHelper::appendWhere($where, 'staffName', getMyProp( $criDatas, 'staffName') );
        $where = MongoHelper::appendWhere($where, 'partBase_.workCompany', getMyProp( $criDatas, 'workCompany') );
        $where = MongoHelper::appendWhere($where, 'partBase_.staffType', getMyProp( $criDatas, 'staffType') );  
        $where = MongoHelper::appendWhere($where, 'partWork_.workStatus', getMyProp( $criDatas, 'workStatus') );
    
        $where = MongoHelper::appendWhere($where, 'vehicleType', getMyProp( $criDatas, 'vehicleType') );
        $where = MongoHelper::appendWhere($where, 'licensePlate',   getMyProp( $criDatas, 'licensePlate') );
        //        
        //$where = MongoHelper::appendWhere($where, 'vehicleDatas.vehicleType', getMyProp( $criDatas, 'vehicleType') );
        //$where = MongoHelper::appendWhere($where, 'vehicleDatas.licensePlate',   getMyProp( $criDatas, 'licensePlate') );
        
        //=============================================================================
        
        
        $columns = array( '_id', 'staffCode', 'staffName', 'active', 'createdAt',  'licensePlate',
            'partBase_', 'partWork_', 'partLicense_', 'partAbsent_');
         
        $sortByConvert = array('phone' => 'partBase_.phone', 'workCompany' =>'partBase_.workCompany',  
            'workStatus' => 'partWork_.workStatus'  );
        
        $output = MongoTable::getOutput(
             self::$TABLE_NAME, $columns , array( "where" => $where, "totalWhere" => $totalWhere , "sortByConvert" => $sortByConvert)
        );
        
                                      
        foreach ($output["aaData"] as &$row)
        {
            $row['mongoId'] =  MongoHelper::getIdByObject($row['_id']);
            $row['staffCode'] = getMyProp($row, 'staffCode', '');
            $row['staffName'] = getMyProp($row, 'staffName', '');      
            $row['phone'] = self::getTableDataInPart($row, 'partBase_', 'phone');                
            $row['workCompany'] = WorkCompany::getDataName(  self::getTableDataInPart($row, 'partBase_', 'workCompany') );  
            $row['workCompanyId'] =   self::getTableDataInPart($row, 'partBase_', 'workCompany' );  
            
            $row['workStatus'] =  Rdb::getWorkStatus(  self::getTableDataInPart($row, 'partWork_', 'workStatus') ); 
            
            $row['licensePlate'] = getMyProp($row, 'licensePlate', '');  
            
        }
        
        $output["message"] = self::errors();
        
        return $output;
    }
    
    public static function getTableDataInPart($row, $partName, $fieldName) {
       $partData = getMyProp($row, $partName, '');  
       $output = getMyProp($partData, $fieldName, '');  
       return $output;
    }
    
    public static function getTableDataLicensePlate($row) {
       $partData = getMyProp($row, 'vehicleDatas', '');  
       $output = array();
       
       // loop;
       return DataHelper::arrayToString($output);
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
    
    public static function getAllDataArray() {
        if (is_null(self::$allDataArray)) {

            $query = DB::table(self::$TABLE_NAME);
            $query->where('active', Rdb::$YES);
            $query->where('accountId', self::getLoginAccountId());            
            $query->orderBy("staffCode", "asc");
            self::$allDataArray =  $query->get();
        }
    
        return self::$allDataArray;
    }    


    //======================================================================
    
    public static function addData($data)
    {
        $data['createdAt'] = MongoHelper::date();
        $data['active'] = Rdb::$YES;        
        $data['accountId'] = self::getLoginAccountId();
                
        $result = DB::table(self::$TABLE_NAME)->insertGetId($data);     
        return true;
    }   
    
    
    public static function editData($keyId, $data) {
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($objectId)) return false;
        
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($data);
        return true;
    }   
    
    
    public static function deleteData($keyId) {
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
        
        DB::table(self::$TABLE_NAME)->where('_id', $keyId )->delete();
        return true;
    }    
     
    // เพื่มข้อมูลทะเบียนรถที่ประจำ
    public static function updateRelateVehicle($inputDatas) {
        $staffIds = VehicleHelper::getRelateStaffIds($inputDatas);        
        $vehicleId = getMyProp($inputDatas, 'vehicleId', '');        
        $licensePlate = getMyProp($inputDatas, 'licensePlate', '');   
        $vehicleType = VehicleHelper::getRelateVehicleType($inputDatas);  
        
        foreach ($staffIds as $staffId) {

           $updateData = array(
               "vehicleId" => $vehicleId,
               "licensePlate" => $licensePlate,
               "vehicleType" => $vehicleType
           );
           
           // myDebug($staffId);
           // myDebug($updateData);
            
           $objectId = MongoHelper::getObjectId($staffId); 
           DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($updateData);
        }
        
        return true;
    }    

    
}




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
use App\Http\Models\Vehicle\Vehicle;
use App\Http\Libraries\FormatHelper;
use App\Http\Libraries\BigPage\VehMonitorHelper;

class VehRepair extends MyBaseModel
{
    
    static protected $TABLE_NAME = 'veh_repair';
    
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
        
        $where = MongoHelper::appendWhere($where, 'vehicleId', getMyProp( $criDatas, 'vehicleId') );
        $where = MongoHelper::appendWhere($where, 'licensePlate', getMyProp( $criDatas, 'licensePlate') );

        if (self::checkValidThaiDate( getMyProp( $criDatas, 'fixDateFrom'), "วันที่ซ่อมบำรุง (จาก)") &&
            self::checkValidThaiDate( getMyProp( $criDatas, 'fixDateTo'), "วันที่ซ่อมบำรุง (ถึง)")
        ) {
            $where = MongoHelper::appendWhereDateRange($where, 'fixStartDate',  
                    getMyProp( $criDatas, 'fixDateFrom'), getMyProp( $criDatas, 'fixDateTo'), false);
        }
        
        $columns = array( '_id', 'vehicleId', 'licensePlate', 'times','fixStartDate', 
            'fixEndDate', 'odometer', 'fixItemDatas',  'cost', 'guaranty', 'monitorTopicDatas',
            'fileDatas');
         
        $output = MongoTable::getOutput(
             self::$TABLE_NAME, $columns , array( "where" => $where, "totalWhere" => $totalWhere)
        );
        
        foreach ($output["aaData"] as &$row)
        {
            $row['mongoId'] =  MongoHelper::getIdByObject($row['_id']);
            $row['vehicleId'] = getMyProp($row, 'vehicleId', '');
            $row['licensePlate'] = getMyProp($row, 'licensePlate', '');
            $row['times'] = getMyProp($row, 'times', '');            
            $row['fixStartDate'] = DateHelper::mongoDateToThai( getMyProp($row, 'fixStartDate', '') , false);  
            
            $row['fixEndDate'] = DateHelper::mongoDateToThai( getMyProp($row, 'fixEndDate', '') , false); 
            $row['odometer'] = getMyProp($row, 'odometer', '');   
            $row['fixItemDatas'] = getMyProp($row, 'fixItemDatas', '');    
            $row['monitorTopicDatas'] = getMyProp($row, 'monitorTopicDatas', '');             
            $row['guaranty'] = getMyProp($row, 'guaranty', '');    
            
            $row['fileDatas'] = FormatHelper::formatImageLinkSimple( getMyProp($row, 'fileDatas', '') );                
        }
        
        $output["message"] = self::errors();
        
        return $output;
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

    
    public static function getDatasByVehicleId($vehicleId) {                         
        if (empty($vehicleId)) return false;
    
        $query = DB::table(self::$TABLE_NAME);
        $query->where("vehicleId", $vehicleId);
        $query->orderBy("fixStartDate", "asc");        
        $result = $query->get();
        
        return $result;
    }


    //======================================================================
    
    public static function addData($data)
    {
        $data['createdAt'] = MongoHelper::date();
        $data['accountId'] = self::getLoginAccountId();
        $data['licensePlate'] = Vehicle::getLicensePlateByVehicleId( $data['vehicleId'] );
        
        $result = DB::table(self::$TABLE_NAME)->insertGetId($data);  
        
        self::updateAfterChange($data['vehicleId']);
        return true;
    }   
    
    
    public static function editData($keyId, $data) {
        $objectId = MongoHelper::getObjectId($keyId);                
        if (empty($objectId)) return false;
        
        $data['licensePlate'] = Vehicle::getLicensePlateByVehicleId( $data['vehicleId'] );
        
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($data);
        
        self::updateAfterChange($data['vehicleId']);        
        return true;
    }   
    
    public static function editSimpleData($keyId, $data) {
        $objectId = MongoHelper::getObjectId($keyId);        
        
        if (empty($objectId)) return false;
        DB::table(self::$TABLE_NAME)->where('_id', $objectId )->update($data);
     
        return true;
    }   
    
    public static function deleteData($keyId) {
        $keyId = MongoHelper::getObjectId($keyId);                
        if (empty($keyId)) return false;
        
        $data = self::getData($keyId);
        $vehicleId = getMyProp($data, 'vehicleId');
        DB::table(self::$TABLE_NAME)->where('_id', $keyId )->delete();
        
        self::updateAfterChange($vehicleId); 
        return true;
    }    
     
    
    //======================================================================
    
    private static function updateAfterChange($vehicleId)
    {
        if (empty($vehicleId)) return false;
        
        $datas = self::getDatasByVehicleId($vehicleId);
        
        
        //======================================= UPATE TIMES
        $times = 0;
        foreach ($datas as $data) {
            $mongoId = MongoHelper::getIdByObject($data['_id']);
            $times++;
            self::editSimpleData( $mongoId ,  array("times" =>  $times));            
        }

        //========================================
        
        
        VehMonitorHelper::updateVehicleLastRepair($vehicleId, $datas);        
        return true;
        
    }  

    
}




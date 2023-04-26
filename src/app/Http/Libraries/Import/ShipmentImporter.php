<?php

namespace App\Http\Libraries\Import;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\MyBaseLib;
use App\Http\Models\AppSetting\AppSetting;
use App\Http\Models\ImportLog\ImportLog;
use App\Http\Models\ImportStatus\ImportStatus;
use App\Http\Models\EngineSys\EngineSys;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Rdb;
use App\Http\Models\ShopeeShop\ShopeeShop;
use App\Http\Models\DaemonStatus\DaemonStatus;
use App\Http\Libraries\Shopee\ShopeeSyncHelper;

    
/* 
 *  สร้าง command  -> php artisan make:command ImportShipment 
 *  เรียกใช้ command -> php artisan import_shipment
 *  
 */

class ShipmentImporter extends MyBaseLib
{

    public static function start() {
        set_time_limit(300);
        
        self::debug("IMPORT SHIPMENT  ======== START");
                                        

        
        if (DaemonStatus::isWorking(Rdb::$DAEMON_SHIPMENT_IMPORTER)) {
            self::debug("IMPORT SHIPMENT ====== PREVIOUS RUN IS WORKING");
            return;
        }
        
        try {
            
            DaemonStatus::setStartInfo(Rdb::$DAEMON_SHIPMENT_IMPORTER);
            
            $shopDatas = ShopeeShop::getAllSystemDataArray();

            foreach ($shopDatas as $shop) {             
                self::importByShop($shop);
            }
            
            
            DaemonStatus::setResult(Rdb::$DAEMON_SHIPMENT_IMPORTER, "success");
        } 
        catch (Exception $e) {
            
           $error = $e->getMessage();
           DaemonStatus::setResult(Rdb::$DAEMON_SHIPMENT_IMPORTER, $error);
        } 
        finally {
           DaemonStatus::setEndInfo(Rdb::$DAEMON_SHIPMENT_IMPORTER);
        }


        self::debug("IMPORT SHIPMENT =========== FINISH");
    }
    
        
    //=========================================================================
    //
    // IMPORT BY TYPE
    //
    //=========================================================================
    
        
    public static function importByShop($shop) {
        $shopId = getMyProp($shop, 'shop_id', '');
        $accountId = getMyProp($shop, 'accountId', '');
        //$branchId = getMyProp($shop, 'branchId', '');
        
        if (empty($shopId) || empty($accountId)) return;
        

        ImportStatus::prepareData($shopId);
        
        $progress = ImportStatus::getData($shopId);
        $lastItemUpdateTimestamp = getMyProp($progress, 'last_item_update_timestamp', '');
        $lastItemCreateTimestamp = getMyProp($progress, 'last_item_create_timestamp', '');
        
        self::debug("IMPORT BY SHOP: $shopId , LAST UPDATE TIMESTAMP:   $lastItemUpdateTimestamp --------------");

        $result = ShopeeSyncHelper::syncWithEachShop($shop, "", $lastItemUpdateTimestamp);
            
        $newCount =  $result['new_count'];
        $updateCount = $result['update_count'];
        $importedDatas = $result['imported_datas'];
        $maxUpdateTime = $result['max_update_time'];
        $maxCreateTime = $result['max_create_time'];    
        
        self::debug("FOUND NEW $newCount , UPDATE: $updateCount, TOTAL: "  . sizeof($importedDatas) 
                ." MAX UPDATE TIME: $maxUpdateTime " );
        
        
        if (sizeof($importedDatas) > 0) {
            ImportLog::addDatas($importedDatas);
        }
        
        //================================================================
     
        $successData = array(
          //  "shop_id" => $shopId,
            "accountId" => $accountId,
            "new_count" => $newCount,
            "update_count" => $updateCount,
            "result" => "Success",
        );
        
        if (!empty($maxCreateTime)) {
            $successData['last_item_create_timestamp'] = $maxCreateTime;
            $successData['last_item_create_time'] = DateHelper::timestampToMongoDate(  $maxCreateTime );
        }
        
        if (!empty($maxUpdateTime)) {
            $successData['last_item_update_timestamp'] = $maxUpdateTime;
            $successData['last_item_update_time'] = DateHelper::timestampToMongoDate(  $maxUpdateTime );
        }
        
        ImportStatus::addSuccess( $shopId, $successData ); 

    } 

    /*
     *         $insertLog = array(
            "file_name" => $fileName,
            "file_name_date_part" => HnpImportHelper::getFileNameDatePart($fileName), 
            "file_date_modified" => $dateModified,
            "dir_path" => $filePath,
            "hnp_type" => Rdb::$HNP_TYPE_HARNESS,
            "engine_sys_id" => $engineSysId,
            "row_count" => sizeof($dataRows),   
            "hnp_date" =>  (!empty($hnpDate))? $hnpDate: null,
            "revision" => ImportLog::getNewRevisionForImport($engineSysId, Rdb::$HNP_TYPE_HARNESS, $hnpDate),  
            "hnp_timestamp" => $hnpTimestamp,
            
        );

        $importId = ImportLog::addData($insertLog);
     * 
     */

    
    //=========================================================================
    //
    // IMPORT FILE
    //
    //=========================================================================
    
    public static function debug($message) {        
        echo "\n".$message;
    }
    
}
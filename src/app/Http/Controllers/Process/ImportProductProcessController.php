<?php
namespace App\Http\Controllers\Process;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DropdownMgr;
use Log;
use App\Http\Models\Rdb;
use App\Http\Models\Product\Product;
use App\Http\Models\MasterProduct\MasterProduct;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\FormatHelper;
use App\Http\Libraries\DateHelper;
use DB;
use App\Http\Libraries\SiteHelper;
use URL;
use App\Http\Models\Product\Manufacturer;
use App\Http\Models\Product\Unit;
use App\Http\Models\Product\Category;

/** อัปเดตรายการสินค้าจากไฟล์  csv */
class ImportProductProcessController extends MyBaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function test(Request $request) {
        $output = array();        
  
        $output['result'] = "OK TEST";
        
        return $this->openView('process.showResult', $output);
    
    }
    

    function start(Request $request) {
        ini_set('max_execution_time', 2500); 
        
        $output = array();
        DataHelper::debug("PROGRESS:: START  ");   
        $result = "";
        
        $url =  URL::asset('product_631002.csv');
        
        //DataHelper::debug("URL: $url ");  
   
        $result = "";        
        $count = 0;       
        $addedCount = 0;
        $addedErrorCount = 0;
        
        $updatedCount = 0;
        $updatedErrorCount = 0;
        
        $manuFacturerCount = 0;
        $allCode = array();
        $dupCode = array();
        
        if (($handle = fopen($url,  "r")) !== FALSE) {

        
             while (($data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
                $num = count($data);
                 
                if ($num >= 14) {

                    $index = DataHelper::trim($data[2]);
                    
                    if (!is_numeric($index)) {
                        continue;
                    }
                    
                    //=======================================================
                    // PREPARE FROM FILE
                    
                    $accountId = Rdb::$ACCOUNT_SYSADMIN;
                    $code = DataHelper::trim($data[3]);                    
                    $fullName = DataHelper::trim($data[6]);
                    $shortName = DataHelper::trim($data[7]);
                    $unit = DataHelper::trim($data[9]);
                    $price = DataHelper::trim($data[10]);
                    $manufacturerName = $this->formatManufacturerName( DataHelper::trim($data[11]) );
                    $barcode = DataHelper::trim($data[13]);
                       
                    $shortName =  (empty($shortName))? $fullName : $shortName;
                    
                    $price = DataHelper::toInteger($price, 0);                    
                    $price = ($price > 0)? $price."": "";

                    $count++;

                    if ($count > 15) continue; 
                   //  DataHelper::debug("$num | $index | $code | $fullName | $shortName | $unit | $price | $manufacturerName | $barcode");   
                
                    
                    if (!in_array($code, $allCode)) {
                        $allCode[] = $code;
                    }
                    else {
                        $dupCode[] = $code;
                    }
                    
              
                    //=======================================================
                    // PREPARE MANUFACTURER
                    
                    
                    $manufacturerData = Manufacturer::getDataByName($manufacturerName);
                    
                    if (empty($manufacturerData) && !empty($manufacturerName)) {
                        $inputDatas = array(
                            'name' => $manufacturerName,
                        );   
                        Manufacturer::addData($inputDatas, $accountId);
                        $manuFacturerCount++;
                        
                        $manufacturerData = Manufacturer::getDataByName($manufacturerName);
                    }
                    
                    //=======================================================
                    // PREPARE UNIT
                    
                    $unitObject = Unit::getDataByName($unit);
                    
                    if (empty($unitObject) && !empty($unit)) {
                        $inputDatas = array(
                            'unit_code' => Rdb::$DEFAULT_UNIT_CODE,
                            'name' => $unit,
                        );   
                        Unit::addData($inputDatas, $accountId);
                    }
                    
                    //=====================================================
                    //  PREPARE TO ADD
                    
                    $inputDatas = array(   
                        'name' => $shortName,
                        'sale_price' => $price,
                        'sale_price_no_vat' => NULL,
                        'look' => '',
                        'manufacturer' => (!empty($manufacturerData))? $manufacturerData['mongoId']: "",
                        'cw_code' => $code,
                        'unit' => $unit,
                        'category_id' => '',
                        'master_lot_plan_id' => '',
                        'fileDatas' => array(),
                    ); 
                    
                    $otherNameDatas = array();
                    $otherNameDatas[] =  array("product_name" => $fullName);
                            
                    $unitDatas = array();
                    $unitDatas[] =  array( 
                        "unit" => $unit,
                        'sku_qty' => '1',
                        'sale_price' => $price,   
                        'barcode' => $barcode,
                    );

                    //=============================================================
                    // ADD
                    
                    $product = MasterProduct::getDataByCwCode($code, $accountId);

                    if (empty($product)) { // ========================== add new   
                        $inputDatas['accountId'] = $accountId;

                        if ( Product::addData($inputDatas, null, $otherNameDatas, $unitDatas)) {     
                           $addedCount++;
                        }   
                        else {
                           $addedErrorCount++;
                        }
                        
                    }
                    else {
                        if ( Product::editData($product['mongoId'], $product['product_code'], $inputDatas, null, $otherNameDatas, $unitDatas)) {     
                           $updatedCount++;
                        }   
                        else {
                           $updatedErrorCount++;
                        }
                    }
                                 
                }                
                

            }
            fclose($handle);
        }

        $dupCodeString = DataHelper::arrayToString($dupCode);
        
        $result = $result."\n =============== IMPORT PRODUCT PROCESS ============== \nTOTAL ROW COUNT: $count  ".
                "\nADDED COUNT:  $addedCount (ERROR: $addedErrorCount) \nUPDATED COUNT: $updatedCount (ERROR: $updatedErrorCount) ".
                "\nDUPICATE CODE:  $dupCodeString \nMANUFACTURER ADDED COUNT: $manuFacturerCount";
        DataHelper::debug("PROGRESS:: FINISH  , ROW COUNT: $count");            
        
        $output['result'] = $result;
        return $this->openView('process.showResult', $output);
    }
    


    function update(Request $request) {
        ini_set('max_execution_time', 2500); 
        
        $output = array();
        DataHelper::debug("PROGRESS:: START  ");   
        $result = "";
        
        $url =  URL::asset('product_631111.csv');
        
        //DataHelper::debug("URL: $url ");  
   
        $result = "";        
        $count = 0;       
        $addedCount = 0;
        $addedErrorCount = 0;
        
        $updatedCount = 0;
        $updatedErrorCount = 0;
        
        $manuFacturerCount = 0;
        $categoryCount = 0;
        
        $allCode = array();
        $dupCode = array();
        
        if (($handle = fopen($url,  "r")) !== FALSE) {

        
             while (($data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
                $num = count($data);
                 
                if ($num >= 14) {

                    $index = DataHelper::trim($data[0]);
                    
                    if (!is_numeric($index)) {
                        continue;
                    }
                    
                    //=======================================================
                    // PREPARE FROM FILE
                    
                    $accountId = Rdb::$ACCOUNT_SYSADMIN;
                    $categoryName = DataHelper::trim($data[2]);                        
                    $code = DataHelper::trim($data[3]);                    
                    $fullName = DataHelper::trim($data[6]);
                    $shortName = DataHelper::trim($data[7]);
                    $unit = DataHelper::trim($data[9]);
                    $price = DataHelper::trim($data[10]);
                    $manufacturerName = $this->formatManufacturerName( DataHelper::trim($data[11]) );
                    $barcode = DataHelper::trim($data[13]);
                    $shortName =  (empty($shortName))? $fullName : $shortName;
                    $price = DataHelper::toInteger($price, 0);                    
                    $price = ($price > 0)? $price."": "";
                    $count++;

                    if ($count > 15) continue; 
                   //  DataHelper::debug(" $categoryName | $num | $index | $code | $fullName | $shortName | $unit | $price | $manufacturerName | $barcode");   
                    
                    
                    //=======================================================
                    // PREPARE CATEGORY
                    
                    $categoryData = Category::getDataByName($categoryName, $accountId);
                    
                    if (empty($categoryData) && !empty($categoryName)) {
                        $inputDatas = array(
                            'category_code' => "000",
                            'name' => $categoryName,
                        );   
                        Category::addData($inputDatas, $accountId);
                        $categoryCount++;
                        
                        $categoryData = Category::getDataByName($categoryName, $accountId);
                    }
                    
                    //=======================================================
                    // PREPARE MANUFACTURER
                    
                    $manufacturerData = Manufacturer::getDataByName($manufacturerName);
                    
                    if (empty($manufacturerData) && !empty($manufacturerName)) {
                        $inputDatas = array(
                            'name' => $manufacturerName,
                        );   
                        Manufacturer::addData($inputDatas, $accountId);
                        $manuFacturerCount++;
                        
                        $manufacturerData = Manufacturer::getDataByName($manufacturerName);
                    }
                    
                    //=====================================================
                    //  PREPARE TO UPDATE
                    
                    $inputDatas = array(   
                        'manufacturer' => (!empty($manufacturerData))? $manufacturerData['mongoId']: "",
                        'category_id' => (!empty($categoryData))? $categoryData['mongoId']: "",
                    ); 
                    

                    //=============================================================
                    // ADD
                    
                    $product = MasterProduct::getDataByCwCode($code, $accountId);

                    if (!empty($product)) { // ========================== add new   
                        if ( MasterProduct::editSimpleData($product['mongoId'], $product['product_code'], $product['name'], $inputDatas)) {     
                           $updatedCount++;
                        }   
                    }
                                 
                }                
                

            }
            fclose($handle);
        }

        $dupCodeString = DataHelper::arrayToString($dupCode);
        
        $result = $result."\n =============== IMPORT PRODUCT PROCESS ============== \nTOTAL ROW COUNT: $count  ".
                "\nUPDATED COUNT: $updatedCount (ERROR: $updatedErrorCount) ".
                "\nCATEGORY ADDED COUNT:  $categoryCount \nMANUFACTURER ADDED COUNT: $manuFacturerCount";
        DataHelper::debug("PROGRESS:: FINISH  , ROW COUNT: $count");            
        
        $output['result'] = $result;
        return $this->openView('process.showResult', $output);
    }
    
    
    private function formatManufacturerName($name) {
        if (empty($name)) return "";
        
        $parts = explode(":", $name);
        
        $index = sizeof($parts);
        
        return DataHelper::trim($parts[$index - 1]);
    }
}







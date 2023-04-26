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
use App\Http\Models\Tmt\TmtMaster;
use App\Http\Models\Product\Product;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\FormatHelper;
use App\Http\Libraries\DateHelper;
use DB;

/** อัปเดตรายการสินค้าจากไฟล์  csv */
class ProductUpdateController extends MyBaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    

    function updateProduct(Request $request) {
        ini_set('max_execution_time', 2500); 
        
        $output = array();
        DataHelper::debug("PROGRESS:: START  ");   
        $result = "";
        
        $url = "D:\product_07052019.csv"; // "D:\product_06112018.csv";

        $count = 0;
        $result = "";
        
        $conflictCount = 0;
        $conflictResult = "";
        
        $dupResult = "";
        
        if (($handle = fopen($url,  "r")) !== FALSE) {
              DataHelper::debug("FILE OPENED  ");   
        
             while (($data = fgetcsv($handle, 1000, ",")) !== FALSE ) {
                $num = count($data);
                
                if ($num == 6 &&   is_numeric( $data[0] )) {

                    $productCode = DataHelper::trim($data[1]);
                    $productName = DataHelper::trim($data[2]);
                    $productName = self::removeUnitFromName($productName);
                    $productName = DataHelper::trim($productName);
                    
                    $unit = DataHelper::trim($data[3]);
                    
                    $products = Product::getDatasByProductCode($productCode);
                    
                    
                    if (empty($products)) { // add new  
                        
                        $count++;                     
                        $result .= "$count ) $productCode ->   $productName  |  $unit \n";    
                        
                        $inputDatas = array( 
                            'accountId' => self::getLoginAccountId(),
                            'product_code' => $productCode,
                            'name' => $productName,
                            'unit' => $unit,
                            'createdAt'=> MongoHelper::date(),
                            'active' => Rdb::$YES,                                  
                        );  
                       
                      //  DB::table("product")->insert($inputDatas);
                         
                         
                    }
                    else if (sizeof($products) > 1) {
                        $product = $products[0];
                        $size = sizeof($products);
   
                        $dupResult .=  $product['product_code']."  ($size)  \n ";                        
                        
                    }
                    else if (sizeof($products) == 1) {

                        $product = $products[0];
                        
                        if (isset($product['name']) && DataHelper::trim($product['name'])  != $productName &&
                                substr($product['name'], 0 ,3) == substr($productName, 0 ,3)
                         ) {
                             
                            $conflictCount++;
                            
                            $conflictResult .= "$conflictCount";
                            $conflictResult .= ",".$product['product_code'];
                            $conflictResult .=  ",@".$product['name']."@" ;
                            $conflictResult .=  ",@".$productName."@\n";
                                                  
                            $inputDatas = array( 
                                'name' => $productName,                             
                            );  
                            
                           DB::table("product")->where('_id', $product['_id'] )->update($inputDatas);                              
                        }
                        
                    }
                    else {
                        $conflictResult .=  "[UNKNOW ERROR] ......... \n";
                    }
                                        
                }                
                

            }
            fclose($handle);
        }

        $result = $result." \n \n CONFICT ============= \n $conflictResult  \n \n DUPLICATE =========== \n $dupResult ";
      //  DataHelper::debug($result);   
        DataHelper::debug("PROGRESS:: FINISH  , ROW COUNT: $count");            
        
        $output['result'] = $result;
        return $this->openView('process.showResult', $output);
    }
    

    function removeUnitFromName($data) {     
        if (strlen($data) <= 0) return $data;
        
        $pos = strrpos($data, '/');
                
        if ($pos !== false) {
            return substr($data, 0, $pos); 
        }   

        return $data; 
    }
}







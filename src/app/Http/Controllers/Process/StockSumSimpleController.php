<?php
namespace App\Http\Controllers\Process;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Http\Models\Stock\Stock;
use App\Http\Models\Stock\StockHistory;
use App\Http\Controllers\MyBaseController;
use App;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DropdownMgr;
use Log;
use App\Http\Models\Rdb;
use App\Http\Models\Tmt\TmtMaster;
use App\Http\Models\Tmt\TmtDetail;
use App\Http\Models\Product\Product;
use App\Http\Models\ReceiveDoc\ReceiveDoc;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\FormatHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\SaleDoc\SaleDoc;
use App\Http\Models\SaleBcDoc\SaleBcDoc;

/** ดึงข้อมูลซื้อขาย ออกมาเป็นไฟล์ csv */
class StockSumSimpleController extends MyBaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    

    function getStockSumFile(Request $request) {
        ini_set('max_execution_time', 2500); 
        
        $store = array();

        // ===============================================  07

        $products = Product::getAllDataArray("bdrug");
        $saleBcDocs = SaleBcDoc::getAll("bdrug");
        $saleDocs = SaleDoc::getAll("bdrug");
        $receiveDocs = ReceiveDoc::getAll("bdrug");

        DataHelper::debug("PROGRESS:: START ");    

        
        foreach ($products as $product) {

            //if (sizeof($store) > 1000) break;
            
            $productId =  MongoHelper::getIdByObject($product['_id']);
            $productName = (!empty($product['name']))? $product['name']: "";
            $productCode = (!empty($product['product_code']))? $product['product_code']: "";
            
            $key = $productId;
            
            if (!isset($store[$key])) {
                $store[$key] = array();
                $store[$key]['code'] = $productCode;
                $store[$key]['name'] = $productName;                
            }
            
            
            //COLLECT SALE ===============================================
            $saleStore = [];            
            foreach ($saleBcDocs as $saleBcDoc) {
                $saleProducts = SaleBcDoc::prepareProductDatasForGet($saleBcDoc);
                
                foreach( $saleProducts as $data) {
                    if ($data['product_id'] == $productId) {
                        $unit = !empty($data['unit'])? $data['unit']: "ไม่มี";
                        if (!isset($saleStore[$unit])) {
                            $saleStore[$unit] = 0;
                        }
                        
                        $saleStore[$unit] += intval($data['qty']);
                    }
                    
                }                
            }
            
            foreach ($saleDocs as $saleDoc) {
                $saleProducts = SaleDoc::prepareProductDatasForGet($saleDoc);
                
                foreach( $saleProducts as $data) {
                    if ($data['product_id'] == $productId) {
                        $unit = !empty($data['unit'])? $data['unit']: "ไม่มี";
                        if (!isset($saleStore[$unit])) {
                            $saleStore[$unit] = 0;
                        }
                        
                        $saleStore[$unit] += intval($data['qty']);
                    }
                    
                }                
            }
            
            $store[$key]['saleDatas']  = $saleStore;       
            
            //COLLECT RECEIVE  ===============================================
         
            $receiveStore = [];            
            foreach ($receiveDocs as $receiveDoc) {
                $receiveProducts = ReceiveDoc::prepareProductDatasForGet($receiveDoc);
                
                foreach( $receiveProducts as $data) {
                    if ($data['product_id'] == $productId) {
                        $unit = !empty($data['unit'])? $data['unit']: "ไม่มี";
                        if (!isset($receiveStore[$unit])) {
                            $receiveStore[$unit] = 0;
                        }
                        
                        $receiveStore[$unit] += intval($data['qty']);
                    }
                    
                }                
            }

            $store[$key]['receiveDatas']  = $receiveStore;       
        }
               
        
        DataHelper::debug("PROGRESS:: COLLECTED  ".sizeof($store));    
     
        //===============================================
        
        $dataRows  = array();        
        $dataRows[] = FormatHelper::arrayToCsvLine(["ลำดับที่", "รหัสสินค้า","ชื่อสินค้า","ซื้อจำนวน","หน่วย","ขายจำนวน","หน่วย"]);
       // ksort($store);
        $index = 1;
        
        foreach ($store  as $data) {
            //$dataRows[] = FormatHelper::arrayToCsvLine([ $data['code'],$data['name'], $data['unit'],$data['qty04'],$data['qty05'],$data['qty06'],$data['qty07'] ]);
                        
            if (sizeof($data['saleDatas']) > 0) {
                $showUnits = [];
                $first = true;
                
                foreach ($data['receiveDatas'] as $unit => $qty) {
                      $showUnits[] = $unit;
                      $saleDataUnit = "";
                      $saleDataQty = "";
                      
                      if (isset($data['saleDatas'][$unit])) {
                          $saleDataUnit = $unit;
                          $saleDataQty = $data['saleDatas'][$unit];
                      }
                      
                      if ($first) {
                          $dataRows[] = FormatHelper::arrayToCsvLine([$index++, $data['code'],$data['name'], $qty, $unit, $saleDataQty, $saleDataUnit ]);                          
                      }
                      else {
                          $dataRows[] = FormatHelper::arrayToCsvLine(["", "", "", $qty, $unit, $saleDataQty, $saleDataUnit ]);
                      }
                    
                      $first = false;
                }   
                
                foreach ($data['saleDatas'] as $unit => $qty) {
                     if (!in_array($unit, $showUnits)) {
                        if ($first) {
                            $dataRows[] = FormatHelper::arrayToCsvLine([$index++, $data['code'],$data['name'], "", "", $qty,  $unit ]);                          
                        }
                        else {
                            $dataRows[] = FormatHelper::arrayToCsvLine(["", "", "", "", "", $qty ,  $unit]);
                        }

                        $first = false;                         
                     }

                }
            
            }
            else {
                // $dataRows[] = FormatHelper::arrayToCsvLine([$index++, $data['code'],$data['name'] ]);
            }

        }
       
        DataHelper::debug("PROGRESS:: FINISH  ".sizeof($dataRows));            
        
        $result = "";
       // $result = implode("," , $docCode);
        $result = implode("\r\n", $dataRows);
        
        $this->data['result'] = "$result";
        return $this->openView('process.getStockSumFile', $this->data);
    }
    

}







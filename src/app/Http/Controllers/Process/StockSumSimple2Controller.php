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

/** ดึงข้อมูลซื้อขาย ออกมาเป็นไฟล์ csv  แบบแยกประเภท ขายไม่มีบิล ขายมีบิล  */
class StockSumSimple2Controller extends MyBaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    

    function getStockSumFile2(Request $request) {
        ini_set('max_execution_time', 2500); 
        
        $store = array();

        // ===============================================  07

        $products = Product::getAllDataArray(); // "bdrug"
        
        $saleBcDocs = SaleBcDoc::getDatasByDocDate("01/01/2561", "31/12/2561");
       // $saleDocs = SaleDoc::getDatasByDocDate("01/01/2561", "31/12/2561");
                
        //$saleBcDocs = SaleBcDoc::getAll();
        //$saleDocs = SaleDoc::getAll();
        //$receiveDocs = ReceiveDoc::getAll("bdrug");

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
            $priceStore = [];
            
            /*   */
            foreach ($saleBcDocs as $saleBcDoc) {
                $saleProducts = SaleBcDoc::prepareProductDatasForGet($saleBcDoc);
                
                foreach( $saleProducts as $data) {
                    
                    if ($data['product_id'] == $productId) {
                        
                        $unit = !empty($data['unit'])? $data['unit']: "ไม่มี";
                        $price = !empty($data['price'])? $data['price']: "";                        
                        
                        if (!isset($priceStore[$unit])) {
                            $priceStore[$unit] = "";
                        }
                        
                        if (!empty($price)) {
                            $priceStore[$unit] = $price;
                        }
                        
                        if (!isset($saleStore[$unit])) {
                            $saleStore[$unit] = 0;
                        }
                        
                        $saleStore[$unit] += intval($data['qty']);
                    }
                }                
            }
         
            /* 
            foreach ($saleDocs as $saleDoc) {
                $saleProducts = SaleDoc::prepareProductDatasForGet($saleDoc);
                
                foreach( $saleProducts as $data) {
                    if ($data['product_id'] == $productId) {
                        
  
                        $unit = !empty($data['unit'])? $data['unit']: "ไม่มี";
                        $price = !empty($data['price'])? $data['price']: "";                        
                        
                        if (!isset($priceStore[$unit])) {
                            $priceStore[$unit] = "";
                        }
                        
                        if (!empty($price)) {
                            $priceStore[$unit] = $price;
                        }
                        
                        if (!isset($saleStore[$unit])) {
                            $saleStore[$unit] = 0;
                        }
                        
                        $saleStore[$unit] += intval($data['qty']);
                    }
                    
                }                
            }
           */
            
            
            $store[$key]['saleDatas']  = $saleStore;       
            $store[$key]['priceDatas']  = $priceStore;  
            
        }
               
        
        DataHelper::debug("PROGRESS:: COLLECTED  ".sizeof($store));    
     
        //===============================================
        
        $dataRows  = array();        
        $dataRows[] = FormatHelper::arrayToCsvLine(["ลำดับที่", "รหัสสินค้า","ชื่อสินค้า","ขายจำนวน","หน่วย"]); // ,"ราคาขาย"
       // ksort($store);
        $index = 1;
        
        foreach ($store as  $data) {
           
            if (sizeof($data['saleDatas']) > 0) {

                $first = true;
                
                foreach ($data['saleDatas'] as $unit => $qty) {
                    $price = (isset($data['priceDatas'][$unit]))? $data['priceDatas'][$unit] : "";
                    
                    if ($first) {
                        $dataRows[] = FormatHelper::arrayToCsvLine([$index++, $data['code'],$data['name'], $qty,  $unit ]);   //, $price                     
                    }
                    else {
                        $dataRows[] = FormatHelper::arrayToCsvLine(["", "", "", $qty ,  $unit]); // , $price
                    }

                    $first = false;                         
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







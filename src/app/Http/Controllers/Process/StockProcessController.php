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
use App\Http\Models\ReceiveDoc\ReceiveDoc100;
use App\Http\Libraries\FormatHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\SaleDoc\SaleDoc;
use App\Http\Models\ReturnDoc\ReturnDoc;

class StockProcessController extends MyBaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    //============================================================================
    // ADJUST
    //============================================================================

    function updateStockOfDoc(Request $request)
    {
        $this->data = array();
        $this->data['result'] = "";
        
        return $this->openView('process.updateStockOfDoc', $this->data);
    
    }
    
    function updateStockOfDocSubmit(Request $request) {
        //return $this->getReceiveQtyByMonth($request);
        ini_set('max_execution_time', 2500); 
        
        $docType = $request->input("docType");
        if ($docType == "receive_doc") {
            return $this->updateStockOfReceiveDoc($request);
        }
        else if ($docType == "return_doc") {
            return $this->updateStockOfReturnDoc($request);
        }
        else if ($docType == "sale_doc") {
            return $this->updateStockOfSaleDoc($request);
        }        
        
        $this->data['result'] = "ไม่พบประเภทเอกสาร";
        return $this->openView('process.updateStockOfDoc', $this->data);
    }
    
    
    function updateStockOfReceiveDoc(Request $request) {
        //return $this->getReceiveQtyByMonth($request);

        
        $date = $request->input('date');
        $toDate = $request->input('to_date');
        
        $datas = ReceiveDoc::getDataRange($date, $toDate);

        $total = sizeof($datas);
        $success = 0;
        $fail = 0;
        $failDatas = array();
        
        foreach ($datas as $data) {
            $receiveDocId = MongoHelper::getIdByObject($data['_id']);
            $docCode = $data['receive_doc_code'];
            $data['product_datas'] = ReceiveDoc::prepareProductDatasForGet($data, false);
            
            $result = Stock::updateDataFromReceiveDoc($receiveDocId, $data, Rdb::$RECEIVE_DOC_TYPE_BILL);
            
            $errors = Stock::getErrors();
            if (sizeof($errors) > 0) {
                $fail++;
                $failDatas[] = "$docCode ไม่สามารถหาสต็อกของ (".implode(',',$errors)." )";
            }
            else {
                $success++;
            }            
        }
        //DataHelper::debug($datas);
    
        $result = "ใบรับสินค้าทั้งหมด  $total , สำเร็จ $success , พบใบที่ผิดพลาด $fail \n";
        $i = 1;
        foreach ($failDatas as $data) {
            $result .=  $i.". ".$data."\n";
            $i++;
        }
        
        $this->data['result'] = $result;
        return $this->openView('process.updateStockOfDoc', $this->data);
    }
    
    //==================================================
    
    function updateStockOfSaleDoc(Request $request) {
        //return $this->getReceiveQtyByMonth($request);
                
        $date = $request->input('date');
        $toDate = $request->input('to_date');
        
        $datas = SaleDoc::getDataRange($date, $toDate);

        $total = sizeof($datas);
        $success = 0;
        $fail = 0;
        $failDatas = array();
        
        foreach ($datas as $data) {
            $docId = MongoHelper::getIdByObject($data['_id']);
            $docCode = $data['sale_doc_code'];
            $data['product_datas'] = SaleDoc::prepareProductDatasForGet($data, false);
            
            $result = Stock::updateDataFromSaleDoc($docId, $data, Rdb::$SALE_DOC_TYPE_BILL);
            
            $errors = Stock::getErrors();
            if (sizeof($errors) > 0) {
                $fail++;
                $failDatas[] = "$docCode ไม่สามารถหาสต็อกของ (".implode(',',$errors)." )";
            }
            else {
                $success++;
            }            
        }
        //DataHelper::debug($datas);
    
        $result = "ใบขายสินค้า ทั้งหมด  $total , สำเร็จ $success , พบใบที่ผิดพลาด $fail \n";
        $i = 1;
        foreach ($failDatas as $data) {
            $result .=  $i.". ".$data."\n";
            $i++;
        }
        
        $this->data['result'] = $result;
        return $this->openView('process.updateStockOfDoc', $this->data);
    }

    //==================================================
    
    function updateStockOfReturnDoc(Request $request) {
        //return $this->getReceiveQtyByMonth($request);
                
        $date = $request->input('date');
        $toDate = $request->input('to_date');
        
        $datas = ReturnDoc::getDataRange($date, $toDate);

        $total = sizeof($datas);
        $success = 0;
        $fail = 0;
        $failDatas = array();
        
        foreach ($datas as $data) {
            $docId = MongoHelper::getIdByObject($data['_id']);
            $docCode = $data['return_doc_code'];
            $data['product_datas'] = ReturnDoc::prepareProductDatasForGet($data, false);
            
            $result = Stock::updateDataFromReturnDoc($docId, $data);
            
            $errors = Stock::getErrors();
            if (sizeof($errors) > 0) {
                $fail++;
                $failDatas[] = "$docCode ไม่สามารถหาสต็อกของ (".implode(',',$errors)." )";
            }
            else {
                $success++;
            }            
        }
        //DataHelper::debug($datas);
    
        $result = "ใบคืนสินค้า ทั้งหมด  $total , สำเร็จ $success , พบใบที่ผิดพลาด $fail \n";
        $i = 1;
        foreach ($failDatas as $data) {
            $result .=  $i.". ".$data."\n";
            $i++;
        }
        
        $this->data['result'] = $result;
        return $this->openView('process.updateStockOfDoc', $this->data);
    }
    
    //=========================================
    
    
    function getReceiveQtyByMonth(Request $request) {

        $store = array();
        $productMode =  false;
        

        
        // ===============================================  07
        $datas = ReceiveDoc100::getDatasByMonth("07","2017");

        foreach($datas as $data) {
            $products = ReceiveDoc::prepareProductDatasForGet($data);
            $billDate = DateHelper::mongoDateToTimestamp( $data['bill_date'] );
             
            foreach ($products as $product) {
                $productId = ($productMode)? $product['product_id']: $product['dealer_product_id'];  
                $qty = $product['qty'];   
                $unit = $product['unit'];
                $key = $productId."-".$unit;
                
                if (!empty($productId)) {
                    if (!isset($store[$key])) {
                        $store[$key] = array();
                        $store[$key]['code'] = ($productMode)? $product['product_code'] : $product['dealer_product_code'];
                        $store[$key]['name'] = ($productMode)? $product['product_name']:  $product['dealer_product_name'];
                        $store[$key]['unit'] = $unit;
                        $store[$key]['qty04'] = 0; $store[$key]['qty05'] = 0; $store[$key]['qty06'] = 0; $store[$key]['qty07'] = 0;
                    }

                    
                    $maxDate04 = DateHelper::sqlToTime("2017-05-01 00:00:00");
                    $maxDate05 = DateHelper::sqlToTime("2017-06-01 00:00:00");
                    $maxDate06 = DateHelper::sqlToTime("2017-07-01 00:00:00");
                    $maxDate07 = DateHelper::sqlToTime("2017-08-01 00:00:00");
               
                    if ($billDate < $maxDate04) {
                        $store[$key]['qty04'] += DataHelper::toInteger($qty);
                    }
                    if ($billDate < $maxDate05) {
                        $store[$key]['qty05'] += DataHelper::toInteger($qty);
                    }
                    if ($billDate < $maxDate06) {
                        $store[$key]['qty06'] += DataHelper::toInteger($qty);
                    }
                    if ($billDate < $maxDate07) {
                        $store[$key]['qty07'] += DataHelper::toInteger($qty);
                    }                    
                    
                }
            }                     
        }        
        
        //===============================================
        
        $dataRows  = array();        
        ksort($store);
        
        foreach ($store  as $data) {
            $dataRows[] = FormatHelper::arrayToCsvLine([ $data['code'],$data['name'], $data['unit'],$data['qty04'],$data['qty05'],$data['qty06'],$data['qty07'] ]);
        }
        
        $result = "";
       // $result = implode("," , $docCode);
        $result = implode("\r\n", $dataRows);
        
        $this->data['result'] = "$result";
        return $this->openView('process.updateStockOfReceiveDoc', $this->data);
    }
    

}







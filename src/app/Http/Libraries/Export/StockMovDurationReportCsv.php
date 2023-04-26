<?php

namespace App\Http\Libraries\Export;
use App\Http\Libraries\CsvExporter;

//ทำการสืบทอดคลาส FPDF ให้เป็นคลาสใหม่
class StockMovDurationReportCsv
{
    
    function generate($input) {
        $csv = new CsvExporter();

        //------------- REPORT HEADER ------------------------//

        $csv->printCsv(array('รายงานความเคลื่อนไหวสินค้าตามช่วงเวลา')); 

        $title = " บริษัท: ".$input['report_company_name'];
        $title .= ", เลขประจำตัวผู้เสียภาษี: ".$input['report_tax_id'];
        $title .= ", วันที่: ".$input['report_date'];
        $title .= ", สาขา: ".$input['report_branch'];
        $title .= ", สมุดบัญขี: ".$input['report_book'];
        
        $csv->printCsv(array($title));         

        $csv->printFp("\n");
        $csv->printCsv(array('ลำดับ','รหัสสินค้า','ชื่อสินค้า','หน่วย', 
            'รายการรับ จำนวน','รายการรับ ราคา/หน่วย','รายการรับ มูลค่า',
            'รายการจ่าย จำนวน','รายการจ่าย ราคา/หน่วย','รายการจ่าย มูลค่า',
            'จำนวนคืน','ปรับสต็อก',
            'คงเหลือ จำนวน','คงเหลือ ราคา/หน่วย','คงเหลือ มูลค่า',            
        ));
        
       for ($i=0; $i < sizeof($input['report_table_datas'] ); $i++) {
             $data = $input['report_table_datas'][$i];
      

            $outputRow = array( 
                $data['order'], 
                $data['product_code'], 
                $data['product_name'], 
                $data['unit'], 
                
                $data['receive_qty'], 
                $data['receive_price'], 
                $data['receive_amount'], 
                
                $data['sale_qty'], 
                $data['sale_price'], 
                $data['sale_amount'], 
                
                $data['return_qty'], 
                $data['adjust_qty'], 
                
                $data['remain_qty'], 
                $data['remain_price'],           
                $data['remain_amount'],                 
             );
            
             $csv->printCsv($outputRow);   
        }
        
      // $csv->printFp("\n END;/");   
           
       // exit;
    }
    
    
}
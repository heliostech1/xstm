<?php

namespace App\Http\Libraries\ExportExcel;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Libraries\ExportExcel\ExcelExporter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Color;

use App\Http\Libraries\DataHelper;
use App\Http\Models\Rdb;
use App\Http\Libraries\ExportExcel\HealthCheckBaseExcel;

class HealthCheckByCompanyExcel extends HealthCheckBaseExcel
{


    public function generate($input) {
        $this->setupSheet();
        

        $this->setContentValue($this->getCurrentPos(), "รายงานสรุปผลการตรวจสุขภาพตามบริษัทลูกค้า", true);
        $this->currentColumn = "A";
        $this->currentRow++; 
        $criDesc = "วันที่ตรวจ: ".$input['reportDate'].
                "    คนขับ: ".$input['reportDriver'].
                "    บริษัทลูกค้า: ".$input['reportCustomerCompany'].
                "    ผลการตรวจ: ".$input['reportCheckResult'];
        
        $this->setContentValue($this->getCurrentPos(), $criDesc);

      
        $this->newLine();
        $this->newLine();
        
        
        //============================================================== TABLE HEADER
        $this->setResultTableHeader($this->getCurrentPos(), "ลำดับ");
        $this->setResultTableHeader($this->getNextColPos(), "วันที่ตรวจ");        
        $this->setResultTableHeader($this->getNextColPos(), "รหัสคนขับ");
        $this->setResultTableHeader($this->getNextColPos(), "ชื่อนามสกุล");
        $this->setResultTableHeader($this->getNextColPos(), "บริษัทลูกค้า");
        $this->setResultTableHeader($this->getNextColPos(), "สรุปผล");   
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจอุณหภูมิ");
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจแอลกอฮอล์");
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจความดัน");
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจสารเสพติด");

        //============================================================== TABLE BODY
        
        $order = 0 ;
        foreach ($input['reportDataRows'] as $checkDate => $itemDataRows) { 
            $orderInGroup = 0;

            foreach ($itemDataRows as $row) {
                $order++;
                $orderInGroup++;
                $tdStyle = "";
                if ($order != 1 && $orderInGroup==1) {
                       //$tdStyle = 'border-top:1px solid #AAA;';
                }
            
                $this->newLine();

                $this->setResultTableCell($this->getCurrentPos(), $order);         
                $this->setResultTableCell($this->getNextColPos(), $row['checkDate']);                
                $this->setResultTableCell($this->getNextColPos(), $row['driverId']);
                $this->setResultTableCell($this->getNextColPos(), $row['driverName']);
                $this->setResultTableCell($this->getNextColPos(), $row['customerCompanyName']);   
                $this->setResultTableCell($this->getNextColPos(), $row['resultIsPass']);    
                $this->setResultTableCell($this->getNextColPos(), $row['isPassTemperature']);
                $this->setResultTableCell($this->getNextColPos(), $row['isPassAlcohol']);
                $this->setResultTableCell($this->getNextColPos(), $row['isPassPressure']);
                $this->setResultTableCell($this->getNextColPos(), $row['isPassDrug']);
          
            }
        }

        
       // $this->setTableContentBorder("A", $startRow, "H", $this->currentRow);
        
        //============================================================== SUMMARY
        
        
        $this->newLine();
        $this->newLine();        
        $this->setContentValue($this->getCurrentPos(), 'ข้อมูลสรุป', true ); 
        
        $this->newLine();
        $this->setContentValue($this->getCurrentPos(), "จำนวนตรวจ: ");
        $this->setContentValue($this->getNextColPos(), $input['reportOutput']['totalCount']);        
        $this->newLine();
        $this->setContentValue($this->getCurrentPos(), "จำนวนผ่าน: ");
        $this->setContentValue($this->getNextColPos(), $input['reportOutput']['passCount']);          
        $this->newLine();
        $this->setContentValue($this->getCurrentPos(), "จำนวนไม่ผ่าน: "); 
        $this->setContentValue($this->getNextColPos(), $input['reportOutput']['failCount']);           
  
        
        
        //============================================================== SUMMARY
        
        self::export($this->spreadsheet, "HealthCheck");
        

    } 
    

}
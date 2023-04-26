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

class DisinfectExcel extends HealthCheckBaseExcel
{


    public function generate($input) {
        $this->setupSheet();



        $this->setContentValue($this->getCurrentPos(), "รายงานผลการตรวจการทำความสะอาดรถด้วยน้ำยาฆ่าเชื้อ", true);
        $this->currentColumn = "A";
        $this->currentRow++; 
        $criDesc = "วันที่ตรวจ: ".$input['reportDate'].
                "    ทะเบียนรถ: ".$input['reportVehicle'].
           //     "    บริษัทลูกค้า: ".$input['reportCustomerCompany'].                
                "    ผลการตรวจ: ".$input['reportCheckResult'];
        
        $this->setContentValue($this->getCurrentPos(), $criDesc);

      
        $this->newLine();
        $this->newLine();
        

                    
                    
        //============================================================== TABLE HEADER
        $this->setResultTableHeader($this->getCurrentPos(), "ลำดับ");
        $this->setResultTableHeader($this->getNextColPos(), "วันที่ตรวจ");        
        $this->setResultTableHeader($this->getNextColPos(), "ทะเบียนรถ");
        $this->setResultTableHeader($this->getNextColPos(), "ผลการตรวจ");
        $this->setResultTableHeader($this->getNextColPos(), "ผู้ตรวจ");
        
        $this->setResultTableHeader($this->getNextColPos(), "รูปภาพ(1)");
        $this->setResultTableHeader($this->getNextColPos(), "รูปภาพ(2)");
        
        //============================================================== TABLE BODY
        
        $imageWidth = 120;
        $imageHeight = 110;
        $rowHeight = 90;
             
        $order = 0 ;
        foreach ($input['reportDataRows'] as $groupKey => $itemDataRows) { 
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
                $this->setResultTableCell($this->getNextColPos(), $row['vehicleId']);
                $this->setResultTableCell($this->getNextColPos(), $row['checkResultExcel']);
                $this->setResultTableCell($this->getNextColPos(), $row['staffDesc']);   
                
                $this->setResultTableImageCell($this->getNextColPos(), $row['image1Link'], $imageWidth, $imageHeight);
                $this->setResultTableImageCell($this->getNextColPos(), $row['image2Link'], $imageWidth, $imageHeight);
                
                
               $this->theSheet->getRowDimension( $this->currentRow )->setRowHeight($rowHeight);
        
          
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
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

class HealthCheckAnalyzeExcel extends HealthCheckBaseExcel
{


    public function generate($input) {
        $this->setupSheet();
        
        $columnWidth = 12;
        $this->theSheet->getColumnDimension("D")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("E")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("F")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("G")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("H")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("I")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("J")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("K")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("L")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("M")->setWidth($columnWidth);   
        $this->theSheet->getColumnDimension("N")->setWidth($columnWidth); 
        $this->theSheet->getColumnDimension("O")->setWidth($columnWidth); 
        $this->theSheet->getColumnDimension("P")->setWidth($columnWidth);  
        
        $this->setContentValue($this->getCurrentPos(), "รายงานวิเคราะห์ผลการตรวจย้อนหลัง", true);
        $this->currentColumn = "A";
        $this->currentRow++; 
        $criDesc = "วันที่ตรวจ: ".$input['reportDate'].
                "    คนขับ: ".$input['reportDriver'];
        
        $this->setContentValue($this->getCurrentPos(), $criDesc);

      
        $this->newLine();
        $this->newLine();
        
        
        //============================================================== TABLE HEADER
        $this->setResultTableHeader($this->getCurrentPos(), "ลำดับ");
        $this->setResultTableHeader($this->getNextColPos(), "รหัสคนขับ");
        $this->setResultTableHeader($this->getNextColPos(), "ชื่อนามสกุล");
        
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจอุณหภูมิ");
        $this->setResultTableHeader($this->getNextColPos(), "");
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจแอลกอฮอล์");
        $this->setResultTableHeader($this->getNextColPos(), "");   
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจความดัน(Sys)");
        $this->setResultTableHeader($this->getNextColPos(), ""); 
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจความดัน(Dia)");
        $this->setResultTableHeader($this->getNextColPos(), ""); 
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจความดัน(Pulse)");
        $this->setResultTableHeader($this->getNextColPos(), "");         
        $this->setResultTableHeader($this->getNextColPos(), "ผลตรวจสารเสพติด");
        $this->setResultTableHeader($this->getNextColPos(), "");
        

        $this->newLine();
        $this->setResultTableHeader($this->getCurrentPos(), "");
        $this->setResultTableHeader($this->getNextColPos(), "");
        $this->setResultTableHeader($this->getNextColPos(), "");
        
        $this->setResultTableHeader($this->getNextColPos(), "ผ่าน");
        $this->setResultTableHeader($this->getNextColPos(), "ไม่ผ่าน");
        
        $this->setResultTableHeader($this->getNextColPos(), "ผ่าน");
        $this->setResultTableHeader($this->getNextColPos(), "ไม่ผ่าน");
        
        $this->setResultTableHeader($this->getNextColPos(), "ผ่าน");
        $this->setResultTableHeader($this->getNextColPos(), "ไม่ผ่าน");
        
        $this->setResultTableHeader($this->getNextColPos(), "ผ่าน");
        $this->setResultTableHeader($this->getNextColPos(), "ไม่ผ่าน");
        
        $this->setResultTableHeader($this->getNextColPos(), "ผ่าน");
        $this->setResultTableHeader($this->getNextColPos(), "ไม่ผ่าน");
        
        $this->setResultTableHeader($this->getNextColPos(), "ผ่าน");
        $this->setResultTableHeader($this->getNextColPos(), "ไม่ผ่าน");
        
        //============================================================== TABLE BODY
        
        $reportDataRows = $input['reportDataRows'];
        
        foreach ($reportDataRows as $row)
        {
            $this->newLine();
 
            $this->setResultTableCell($this->getCurrentPos(), $row['counter']);            
            $this->setResultTableCell($this->getNextColPos(), $row['driverId']);
            $this->setResultTableCell($this->getNextColPos(), $row['driverName']);
            
            $this->setResultTableCell($this->getNextColPos(), $row['temperaturePass']);            
            $this->setResultTableCell($this->getNextColPos(), $row['temperatureFail']);

            $this->setResultTableCell($this->getNextColPos(), $row['alcoholPass']);
            $this->setResultTableCell($this->getNextColPos(), $row['alcoholFail']);
            
            $this->setResultTableCell($this->getNextColPos(), $row['pressureSysPass']);
            $this->setResultTableCell($this->getNextColPos(), $row['pressureSysFail']);
            $this->setResultTableCell($this->getNextColPos(), $row['pressureDiaPass']);
            $this->setResultTableCell($this->getNextColPos(), $row['pressureDiaFail']);
            $this->setResultTableCell($this->getNextColPos(), $row['pressurePulsePass']);
            $this->setResultTableCell($this->getNextColPos(), $row['pressurePulseFail']);
            
            $this->setResultTableCell($this->getNextColPos(), $row['drugPass']);
            $this->setResultTableCell($this->getNextColPos(), $row['drugFail']);            
            
         
        }

        
        //============================================================== SUMMARY
        
        self::export($this->spreadsheet, "HealthCheck");
        

    } 
    

}
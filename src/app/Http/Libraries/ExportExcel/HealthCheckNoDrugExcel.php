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

class HealthCheckNoDrugExcel extends HealthCheckBaseExcel
{


    public function generate($input) {
        $this->setupSheet();
        

        $this->setContentValue($this->getCurrentPos(), "รายงานบุคคลที่ไม่ได้ตรวจสารเสพติด", true);
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
        $this->setResultTableHeader($this->getNextColPos(), "วันที่ตรวจล่าสุด");

        //============================================================== TABLE BODY
        
        $reportDataRows = $input['reportDataRows'];
        
        foreach ($reportDataRows as $row)
        {
            $this->newLine();
            $this->setResultTableCell($this->getCurrentPos(), $row['counter']);            
            $this->setResultTableCell($this->getNextColPos(), $row['driverId']);
            $this->setResultTableCell($this->getNextColPos(), $row['driverName']);
            $this->setResultTableCell($this->getNextColPos(), $row['lastCheckDrug']);     

         
        }

        
        self::export($this->spreadsheet, "HealthCheck");
        

    } 
    

}
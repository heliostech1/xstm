<?php

namespace App\Http\Libraries\ExportVcExcel;

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

class VcVehicleSumExcel extends HealthCheckBaseExcel
{


    public function generate($input) {
        $this->setupSheet();
        
        $columnWidth = 15;
        $this->theSheet->getColumnDimension("A")->setWidth(10);        
        $this->theSheet->getColumnDimension("D")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("E")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("F")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("G")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("H")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("I")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("J")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("K")->setWidth($columnWidth);
     //   $this->theSheet->getColumnDimension("L")->setWidth($columnWidth);  
     //   $this->theSheet->getColumnDimension("M")->setWidth($columnWidth);
     //   $this->theSheet->getColumnDimension("N")->setWidth($columnWidth);
        //---------------------------------------------------------------

        $this->setContentValue($this->getCurrentPos(), "รายงานการบำรุงรักษารถ", true);
        $this->currentColumn = "A";
        $this->currentRow++; 
        $criDesc = "เดือน: ".$input['reportDate'].
                "    ทะเบียนรถ: ".$input['reportVehicle'];

        
        $this->setContentValue($this->getCurrentPos(), $criDesc);

      
        $this->newLine();
        $this->newLine();
        
        
        //============================================================== TABLE HEADER
        $this->setResultTableHeader($this->getCurrentPos(), "ลำดับ");
        $this->setResultTableHeader($this->getNextColPos(), "ทะเบียนรถ");
        $this->setResultTableHeader($this->getNextColPos(), "ตำแหน่ง");
        
                    
        foreach ($input['vcGroupList'] as $vcGroup) {
            $title =  $input['vcGroupInfo'][$vcGroup.'_thaiTitle'];

            $this->setResultTableHeader($this->getNextColPos(), "$title");  
        }

           /*
        $this->setResultTableHeader($this->getNextColPos(), "ทำความสะอาด");
        $this->setResultTableHeader($this->getNextColPos(), "เช็คของเหลว");        
        $this->setResultTableHeader($this->getNextColPos(), "ไล่น้ำถังลม");
        $this->setResultTableHeader($this->getNextColPos(), "เป่ากรอง");
        $this->setResultTableHeader($this->getNextColPos(), "เข็คยาง");
        $this->setResultTableHeader($this->getNextColPos(), "อุปกรณ์ประจำรถ");
        $this->setResultTableHeader($this->getNextColPos(), "อุปกรณ์PPE");        
        $this->setResultTableHeader($this->getNextColPos(), "สมุดประวัติ");
        */
        //$this->setResultTableHeader($this->getNextColPos(), "รวมค่าตอบแทน");
        //$this->setResultTableHeader($this->getNextColPos(), "ค่าปรับ");
       // $this->setResultTableHeader($this->getNextColPos(), "รวมค่าตอบแทน\nหักค่าปรับ");
        
        //============================================================== TABLE BODY
 
        $reportDataRows = $input['reportDataRows'];
        
        foreach ($reportDataRows as $row)
        {
            $this->newLine();
 
            $this->setResultTableCell($this->getCurrentPos(), $row['counter']);            
            $this->setResultTableCell($this->getNextColPos(), $row['vehicleId']);
            $this->setResultTableCell($this->getNextColPos(), $row['vehiclePosition']);
            
            foreach ($input['vcGroupList'] as $vcGroup) {
                $this->setResultTableCell($this->getNextColPos(), $row[$vcGroup.'Count']);  
            }

            
       }




        //============================================================== SUMMARY
        
        self::export($this->spreadsheet, "VcRewardReport");
        

    } 
    

}
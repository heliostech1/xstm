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

class VcRepairExcel extends HealthCheckBaseExcel
{

    public function generate($input) {
        $this->setupSheet();

        $this->theSheet->getColumnDimension("A")->setWidth(10);        


        //---------------------------------------------------------------

        $this->setContentValue($this->getCurrentPos(), "รายงานนัดซ่อมรถ", true);
        $this->currentColumn = "A";
        $this->currentRow++; 
        $criDesc = "วันที่นัดซ่อม: ".$input['reportDate'].
                "    สถานะ: ".$input['reportStatus'];

        
        $this->setContentValue($this->getCurrentPos(), $criDesc);

      
        $this->newLine();
        $this->newLine();

        //============================================================== TABLE HEADER
        $this->setResultTableHeader($this->getCurrentPos(), "ลำดับ");
        $this->setResultTableHeader($this->getNextColPos(), "วันที่นัดซ่อม");
        $this->setResultTableHeader($this->getNextColPos(), "สถานที่");
        $this->setResultTableHeader($this->getNextColPos(), "ทะเบียนรถ");
        $this->setResultTableHeader($this->getNextColPos(), "จุดที่เสีย");
        
        $this->setResultTableHeader($this->getNextColPos(), "ปัญหา");
        $this->setResultTableHeader($this->getNextColPos(), "สถานะ");
        
        //============================================================== TABLE BODY
 
        $reportDataRows = $input['reportDataRows'];
        $counter = 1;
        
        foreach ($reportDataRows as $dateDataRows)
        {
            foreach ($dateDataRows as $placeDataRows)
            {            
                foreach ($placeDataRows as $row)
                {            
                    $this->newLine();

                    $this->setResultTableCell($this->getCurrentPos(), $counter++);            
                    $this->setResultTableCell($this->getNextColPos(), $row['dueDate']);
                    $this->setResultTableCell($this->getNextColPos(), $row['place']);
                    $this->setResultTableCell($this->getNextColPos(), $row['vehicleId']);
                    $this->setResultTableCell($this->getNextColPos(), $row['brokenItem']);
                    
                    $this->setResultTableCell($this->getNextColPos(), $row['issue']);
                    $this->setResultTableCell($this->getNextColPos(), $row['status']);
                    //==================================

                }
                
            }
       }




        //============================================================== SUMMARY
        
        self::export($this->spreadsheet, "VcRepairReport");
        

    } 
    

}
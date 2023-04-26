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

class VcTireNumberExcel extends HealthCheckBaseExcel
{

    public function generate($input) {
        $this->setupSheet();

        $this->theSheet->getColumnDimension("A")->setWidth(10);        

        //---------------------------------------------------------------

        $this->setContentValue($this->getCurrentPos(), "รายงานประวัติเบอร์ยาง", true);
        $this->currentColumn = "A";
        $this->currentRow++; 
        $criDesc = "ทะเบียนรถ: ".$input['reportVehicle'];

        $this->setContentValue($this->getCurrentPos(), $criDesc);

      
        $this->newLine();
        $this->newLine();
        

        //============================================================== TABLE HEADER
        $this->setResultTableHeader($this->getCurrentPos(), "ลำดับ");
        $this->setResultTableHeader($this->getNextColPos(), "เบอร์ยาง");
        $this->setResultTableHeader($this->getNextColPos(), "ล้อที่");
        $this->setResultTableHeader($this->getNextColPos(), "ยี่ห้อ");
        $this->setResultTableHeader($this->getNextColPos(), "วันที่บันทึก");

        
        //============================================================== TABLE BODY
 
        $reportDataRows = $input['reportDataRows'];
        
        $counter = 1;
        foreach ($reportDataRows as $row)
        {
            $this->newLine();
 
            $this->setResultTableCell($this->getCurrentPos(), $counter++);            
            $this->setResultTableCell($this->getNextColPos(), $row['number']);
            $this->setResultTableCell($this->getNextColPos(), $row['position']);
            $this->setResultTableCell($this->getNextColPos(), $row['brand']);
            $this->setResultTableCell($this->getNextColPos(), $row['createDate']);

            
       }


        //============================================================== SUMMARY
        
        self::export($this->spreadsheet, "VcTireNumberReport");
        

    } 
    

}
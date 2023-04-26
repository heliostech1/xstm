<?php

namespace App\Http\Libraries\ExportExcel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Libraries\DataHelper;

class ExcelExporter
{
    
    protected $output;
    
    public function __construct() {
       //$this->output = fopen('php://output', 'w');
      // $this->printHeader();
        set_time_limit(90);
        
    }    
    
    public function export( $spreadsheet, $fileName ) {

        $streamedResponse = new StreamedResponse();
        $streamedResponse->setCallback(function () use ($spreadsheet) {

              $writer =  new Xlsx($spreadsheet);
              $writer->save('php://output');
        });
        
        $streamedResponse->setStatusCode(Response::HTTP_OK);
        $streamedResponse->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $streamedResponse->headers->set('Content-Disposition', 'attachment; filename="'.$fileName.'.xlsx"');
        return $streamedResponse->send();
    }

    public function setEncoding($data) {
        return iconv( 'UTF-8','TIS-620' , $data ); // ที่ต้องแปลงเป็น tis-620 เพราะพอเปิดด้วย excel มันไม่ support utf-8
    }

    
}
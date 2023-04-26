<?php

namespace App\Http\Libraries;

class CsvExporter
{
    
    protected $output;
    
    public function __construct() {
       $this->output = fopen('php://output', 'w');
       $this->printHeader();
    }    
    
    function printHeader() {
        header('Content-Encoding: TIS-620'); 
        header('Content-Type: text/csv; charset=TIS-620'); // utf-8
        header('Content-Disposition: attachment; filename=data.csv');
    }

    function setEncoding($data) {
        return iconv( 'UTF-8','TIS-620' , $data ); // ที่ต้องแปลงเป็น tis-620 เพราะพอเปิดด้วย excel มันไม่ support utf-8
    }

    function printCsv($data_arr) {
        foreach ($data_arr as &$data) {
            $data = $this->setEncoding( $data ); 
        } 
        fputcsv($this->output, $data_arr);
    }
    
    function printFp($data) {
        $data = $this->setEncoding( $data ); 
        fputs($this->output, $data);
    }

    
}
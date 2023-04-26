<?php

namespace App\Http\Libraries\ExportPdf;
use Barryvdh\DomPDF\PDF;

class TestDomPdf 
{

    function generate($inputData) {
        $data = [
            'title' => 'Welcome to ItSolutionStuff.com',
            'date' => date('m/d/Y')
        ];
          
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('exportPdf/myTestPdf', $data);

       // $pdf = PDF::loadView('exportPdf/myTestPdf', $data);
    
        return $pdf->stream('xxxx.pdf');        
       // return $pdf->download('myTestPdf.pdf');
    }

    
}        
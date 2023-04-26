<?php

namespace App\Http\Libraries\ExportPdf;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Http\Libraries\DataHelper;
use TCPDF_FONTS;

//ทำการสืบทอดคลาส FPDF ให้เป็นคลาสใหม่
class MyBaseTcpdf 
{
   // public $SIZE_NORMAL = 11; // 9 13

    
    function printCellHeader($pdf, $w, $h, $txt, $border=0, $align = "C") {
        $border=0;
        $html=false;
        
        $this->printHeaderMuliCell($pdf, $w, $h, $txt, $border, $align, false, 0, '', '', true, 0, $html, true, $h, 'M', false);
    }
    
    function printFrameHeader($pdf, $w, $h) {
        $border=1;
        $html=false;
        
        $this->printHeaderMuliCell($pdf, $w, $h, "", $border, "C", false, 0, '', '', true, 0, $html, true, $h, 'M', false);
    }

    
    function printHeaderTitle($pdf, $w, $h, $txt) {
        $border=0;
        $html=true;

        $this->printHeaderMuliCell($pdf, $w, $h, $txt, $border, "C", false, 0, '', '', true, 0, $html, true, $h, 'T', false);
    }    
    
    function printHeaderName($pdf, $w, $h, $txt) {
        $border=0;
        $html=false;
       // $pdf->SetFont($this->MY_FONT,'B', $this->SIZE_NORMAL);
        
        $this->printHeaderMuliCell($pdf, $w, $h, $txt, $border, "L", false, 0, '', '', true, 0, $html, true, $h, 'T', false);
    }
    
    function printHeaderValue($pdf, $w, $h, $txt, $html=false) {
        $border=0;
       // $pdf->SetFont($this->MY_FONT,'', $this->SIZE_NORMAL);
        
        $this->printHeaderMuliCell($pdf, $w, $h, $txt, $border, "L", false, 0, '', '', true, 0, $html, true, $h, 'T', false);
    }
    
    function printHeaderBlock($pdf, $w, $h, $txt, $html=false) {        
        $border=1;
       // $pdf->SetFont($this->MY_FONT,'', $this->SIZE_NORMAL);
        
        $this->printHeaderMuliCell($pdf, $w, $h, $txt, $border, "C", false, 0, '', '', true, 0, $html, true, $h, 'T', false);
    }   
    
    function printHeaderCell($pdf, $w, $h, $txt) {
        $border=1;
        $html=false;
        //$pdf->SetFont($this->MY_FONT,'', $this->SIZE_NORMAL);
    
        $this->printHeaderMuliCell($pdf, $w, $h, $txt, $border, "C", true, 0, '', '', true, 0, $html, true, $h, 'M', false);
    }
    
    function printHeaderMuliCell($pdf, $w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='',
            $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false){
    
       $pdf->MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y,$reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }   
    
    
    
    //==================================================================
    
    function printRowTableHeader($pdf, $w, $h, $txt) {
        $border=1;
        $html=false;
        //$pdf->SetFont($this->MY_FONT,'', $this->SIZE_NORMAL);
    
        $this->printRowMuliCell($pdf, $w, $h, $txt, $border, "C", true, 0, '', '', true, 0, $html, true, $h, 'M', false);
    }
    
    function printRowFrame($pdf, $w, $h, $txt="") {
        $border=1;
        $html=false;
    
        $this->printRowMuliCell($pdf, $w, $h, $txt, $border, "C", false, 0, '', '', true, 0, $html, true, $h, 'T', false);
    }
    
    function printRowCell($pdf, $w, $h, $txt, $align = "L", $html=false) {
        $border=0;
      
    
        $this->printRowMuliCell($pdf, $w, $h, $txt, $border, $align, false, 0, '', '', true, 0, $html, true, $h, 'T', false);
    }
    
    function printRowSummary($pdf, $w, $h, $txt="", $border=1, $align="C") {        
        $html=false;
    
        $this->printRowMuliCell($pdf, $w, $h, $txt, $border, $align, true, 0, '', '', true, 0, $html, true, $h, 'M', false);
    }    
    
    function printRowMuliCell($pdf, $w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='',
            $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false){
    
        $pdf::MultiCell($w, $h, $txt, $border, $align, $fill, $ln, $x, $y,$reseth, $stretch, $ishtml, $autopadding, $maxh, $valign, $fitcell);
    }
    
    
}
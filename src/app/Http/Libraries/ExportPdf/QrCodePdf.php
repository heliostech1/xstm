<?php

namespace App\Http\Libraries\ExportPdf;
use Elibyy\TCPDF\Facades\TCPDF;
use App\Http\Libraries\ExportPdf\MyBaseTcpdf;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Models\AppSetting\AppSetting;

use TCPDF_FONTS;

use Imagick;
use ImagickPixel;


//ทำการสืบทอดคลาส FPDF ให้เป็นคลาสใหม่
class QrCodePdf extends MyBaseTcpdf
{
    private $REVISION = "1.1";
    
    public $MY_FONT = "freeserif"; //"thsarabun"; //angsa cordia cordiaupc  thsarabun  freeserif courier
    public $MY_LAO_FONT = "phetsarathot";

    public $MY_BORDER = 0;
    
    public $SIZE_TINY = 6;  // 7 10    
    public $SIZE_SMALL2 = 7;  // 7 10    
    public $SIZE_SMALL = 8;  // 7 10
    public $SIZE_NORMAL =  9; // 9 13
    public $SIZE_LARGE = 13; // 9 13    
    public $SIZE_LARGE2 = 16; // 9 13   
    
    public $SIZE_PAGE_NUM = 10;  
    public $SIZE_TABLE_ROW = 8;  
    public $SIZE_REPORT_TITLE = 13;    
    public $SIZE_REPORT_LOGO_TEXT = 12;  
    
    public $SIZE_BIG = 20; // 13 18
    public $SIZE_BIG2 = 24; // 13 18
    public $SIZE_BIG3 = 28; // 13 18
    public $SIZE_BIG4 = 32; 
    
    public $SD_STANDARD_SIZE = 13; 
    public $SD_STANDARD_LAO_SIZE = 11;     
    public $SD_STANDARD_HEIGHT = 4; 
        
    private $current_font_size = null;
    private $current_font_style = null;
    private $current_font_name = null;
  
    public $pageWidth = 198; // a4 = 210 X 297
    public $input = null;
    public $doc_no = "";
    public $print_by = "";
    public $print_time = "";
    public $print_time_short = "";    
    public $print_date = "";   

    
    function generate($inputData) {
       // $width = 100;
       // $height = 100;

       // $pageLayout = array($width, $height); //  or array($height, $width) 
      //  $myPdf = new TCPDF('p', 'pt', $pageLayout, true, 'UTF-8', false);
        
        $myPdf = new TCPDF();
       // $myPdf::setPageFormat("LETTER", "P");
        
        //$this->doc_no = $this->get_document_no();
        $this->print_time =  date('H:i:s');
        $this->print_time_short =  date('H:i');        
        $this->print_date =  date('d').'/'. date('m').'/'.(  date('Y'));        
        $this->print_by = "--";
        $this->input = $inputData;
                      
       // $myPdf::setFontSubsetting(false);
        $myPdf::SetAuthor( 'heliostech' );
        $myPdf::SetCreator( 'heliostech' );
        $myPdf::SetKeywords( 'Print' );
        $myPdf::SetSubject( 'Print' );
        $myPdf::SetTitle( 'Print' );
        
      //  $myPdf::setHeaderMargin(4);               
      //  $myPdf::SetDisplayMode( 'real' , 'continuous' );   
        $myPdf::SetFillColor(255,255,255);
        $myPdf::SetMargins(3, 3, 3);  // margin $left, $top, $right

        $myPdf::AddFont($this->MY_FONT,'', $this->MY_FONT.'.php');
        $myPdf::AddFont($this->MY_FONT,'B',$this->MY_FONT.'b.php');
        $myPdf::AddFont($this->MY_FONT,'I',$this->MY_FONT.'i.php');
               

        $myPdf::AddPage("P","A6");
        $this->setContent($myPdf);

        //====================================================
        $root =  config('app.rootStorage');     
        $filename = uniqid(rand(), true) . '.pdf';
        $filePath = $root."/".$filename;

        $myPdf::Output( $filePath , 'F' );       //  F = Save to file
        
       // $pdf_out = $pdf->Output($pdf_filename, 'S'); // returns pdf string
        $im = new Imagick();

        // TODO :: pdf -> Image ต้องลง GhostScript ????
        $im->setResolution(150,150);
        $im->readImage($filePath); 
        $im->setImageFormat('jpeg');    
        $im->writeImage('qrCode.jpg'); 
        
        $im->clear(); 
        $im->destroy();

        unlink($filePath);
        
    }
    
    function setContent($pdf) {
        myDebug($this->input);
        
        $width = 100;
        
        $pdf::SetFont($this->MY_FONT,'', 22);
        $this->printRowCell($pdf , $width, 10, $this->input['label1'] , "C"); // $currY  
        $pdf::Ln(12);
        
        $pdf::SetFont($this->MY_FONT,'', 17);
        $this->printRowCell($pdf , $width, 10, $this->input['label2'], "C"); // $currY  
        
    }
}
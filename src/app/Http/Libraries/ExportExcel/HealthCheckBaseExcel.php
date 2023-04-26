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

class HealthCheckBaseExcel extends ExcelExporter
{
    protected $spreadsheet = null;    
    protected $theSheet = null;
    
    protected $currentColumn = "A";
    protected $currentRow = 1;
    protected $lastColumn = null;
    
    
    private $widthBase = 15;
    private $widthLarge = 15;
    
    public function getWidth($name) {
        if ($name == 'base') return 9;
        if ($name == 'base2') return 12;        
        if ($name == 'large') return 15;
        if ($name == 'large2') return 19;    
        if ($name == 'large3') return 27;           
    }
    
    public function setupSheet() {

        $this->spreadsheet = new Spreadsheet();
        $this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $this->spreadsheet->getDefaultStyle()->getFont()->setSize(11); 
      //  $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $this->spreadsheet->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        $this->theSheet = $this->spreadsheet->getActiveSheet();
        
        
        //=====================================
        
        
        $this->theSheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);
        $this->theSheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

        
        $this->theSheet->getPageSetup()->setFitToWidth(1);
        $this->theSheet->getPageSetup()->setFitToHeight(0);
         
        $this->theSheet->getPageMargins()->setTop(0.35);
        $this->theSheet->getPageMargins()->setBottom(0.7);
        $this->theSheet->getPageMargins()->setLeft(0.35);
        $this->theSheet->getPageMargins()->setRight(0.35);
        
        $this->theSheet->getPageMargins()->setHeader(0);
        $this->theSheet->getPageMargins()->setFooter(0.3);
        
        
        $this->theSheet->getHeaderFooter()->setOddFooter('&L '.config('app.appTitle').' &B' . '&R  Pages &P / &N');
        
        //=========================================
        
        
        $columnWidth = 20;
        $this->theSheet->getColumnDimension("A")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("B")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("C")->setWidth($columnWidth);
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
    }
    
    
    public function newLine() {
        $this->currentColumn = "A";
        $this->currentRow++; 
    }  
    
    public function getCurrentPos() {
        return $this->currentColumn.$this->currentRow; 
    }
    
    public function getNextColPos() {
        $this->currentColumn++;
        return $this->currentColumn.$this->currentRow;       
    }    
    
    
    public function setTableContentBorder($startCol, $startRow, $endCol, $endRow) {
        if ($endRow >= $startRow) {
            $theStyle = $this->theSheet->getStyle($startCol.$startRow.":".$endCol.$endRow);        
            $theStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);   
        }
        
    }
    
    public function setResultTableHeader($pos, $value) {
        $this->theSheet->getStyle($pos)->getAlignment()->setWrapText(true);

        $this->setContentValue($pos, $value, true, true, "eeeeee"); // "eeeeee"

    }
    
    
    public function setResultTableCell($pos, $value, $bold=false, $border=false, $bgColor=false) {        
        $this->setContentValue($pos, $value, $bold, true, $bgColor);    
    }

    
    public function setResultTableImageCell($pos, $src, $width, $height) {    
        $this->setContentValue($pos, "", false, true);   
        
        if (empty($src)) {
            return;
        }
        
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        //$drawing->setName('Paid');
       // $drawing->setDescription('Paid');
        $drawing->setPath($src); /* put your path and image here */
        $drawing->setCoordinates($pos);
        $drawing->setWidth($width);
        $drawing->setHeight($height);

        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
       // $drawing->setRotation(25);
      //  $drawing->getShadow()->setVisible(true);
       // $drawing->getShadow()->setDirection(45);
        $drawing->setWorksheet( $this->theSheet ); 
    }

    
    
    public function setResultTableRow($name, $value1, $value2) {
        $this->currentColumn = "A";
        $this->currentRow++; 
        
        $this->setContentValue($this->getCurrentPos(), $name);
        $this->setContentValue($this->getNextColPos(), $value1);
        $this->setContentValue($this->getNextColPos(), $value2);        
    }

    
    public function setContentValue($pos, $value, $bold=false, $border=false, $bgColor=false) {
        $this->theSheet->setCellValue($pos, $value);        
        $theStyle = $this->theSheet->getStyle($pos);
        
        if ($bold) {
            $theStyle->getFont()->setBold(true); 
        }

        if ($border) {
            $theStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
        
        if (!empty($bgColor)) {
           $theStyle->getFill()->setFillType(Fill::FILL_SOLID);        
           $theStyle->getFill()->getStartColor()->setRGB($bgColor);
        }

        
    }
    
}
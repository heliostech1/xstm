<?php

namespace App\Http\Libraries\ExportExcel;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Libraries\Export\ExcelExporter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use App\Http\Libraries\DataHelper;
use App\Http\Models\Rdb;

class BomCompareExcel extends ExcelExporter
{
    
    private $theSheet = null;
    private $currentColumn = "A";
    private $currentRow = 1;
    private $lastColumn = null;
    
    private $widthBase = 15;
    private $widthLarge = 15;

    public function setupPrint() {

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
        
        
       // $this->theSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(3, 3);
        
        

    }
    
    public function generate($input) {

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(11); 
      //  $spreadsheet->getDefaultStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getDefaultStyle()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        
        $this->theSheet = $spreadsheet->getActiveSheet();
        $this->setupPrint();
        
        $columnWidth = 30;
        $this->theSheet->getColumnDimension("A")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("B")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("C")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("D")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("E")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("F")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("G")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("H")->setWidth($columnWidth);
        $this->theSheet->getColumnDimension("I")->setWidth($columnWidth);
        
        //-----------------------------------------------------------------------
        // RESULT
        
        $this->setContentValue($this->getCurrentPos(), "BOM COMPARE", true);
        
        $this->currentColumn = "A";
        $this->currentRow++; 
        $this->currentRow++;
        $this->setContentValue($this->getCurrentPos(), "Result", true);
        $this->setContentValue($this->getNextColPos(), $input['message']);
        
        $this->currentColumn = "A";
        $this->currentRow++; 

        $this->setResultTableHeader($this->getCurrentPos(), "Detail Name");
        $this->setResultTableHeader($this->getNextColPos(), "Value");
        
                
        $startRow = $this->currentRow+1;        
        $this->setResultTableRow("BOM Filename", $input['bom_file_name']);
        $this->setResultTableRow("Effective Date", $input['input_date']);
        $this->setResultTableRow("Model Year", $input['model_year']);
        $this->setResultTableRow("Suffix Model", $input['suffix_model']);
        $this->setResultTableRow("Model Group", $input['engine_group']);
        $this->setResultTableRow("Bom Total Items Count", $input['bom_row_count']);
        $this->setResultTableRow("Part List Total Items Count", $input['part_list_row_count']);
        $this->setResultTableRow("Conflict Items Count", $input['only_in_bom_count']);
        $this->setResultTableRow("Only In BOM Items Count", $input['only_in_part_list_count']);
        $this->setResultTableRow("Only In Part List Items Count", $input['conflict_count']);  
        

        $this->setTableContentBorder("A", $startRow, "B", $this->currentRow);
        
        //------------------------------------------------------------
        // CONFLICT DATAS
        
        $this->currentColumn = "A";
        $this->currentRow++; 
        $this->currentRow++; 
           
        $this->setContentValue($this->getCurrentPos(), "Conflict Datas", true);
        
        $this->currentColumn = "A";
        $this->currentRow++; 
        
                            
        $this->setResultTableHeader($this->getCurrentPos(), "UPC");
        $this->setResultTableHeader($this->getNextColPos(), "FNA");
        $this->setResultTableHeader($this->getNextColPos(), "P/No");
        $this->setResultTableHeader($this->getNextColPos(), "P/Name");
        $this->setResultTableHeader($this->getNextColPos(), "Order Type");
        $this->setResultTableHeader($this->getNextColPos(), "Qty (BOM)");
        $this->setResultTableHeader($this->getNextColPos(), "V.Name (BOM)");
        $this->setResultTableHeader($this->getNextColPos(), "Qty (Part List)");
        $this->setResultTableHeader($this->getNextColPos(), "V.Name (Part List)");
        
        $startRow = $this->currentRow+1;   
        
        foreach ($input["conflict_datas"] as $row)
        {
            $this->currentColumn = "A";
            $this->currentRow++; 

            $this->setContentValue($this->getCurrentPos(), $row['upc']);
            $this->setContentValue($this->getNextColPos(), $row['fna']);
            $this->setContentValue($this->getNextColPos(), $row['p_no']);
            $this->setContentValue($this->getNextColPos(), $row['p_name']);
            $this->setContentValue($this->getNextColPos(), $row['order_type']);
            $this->setContentValue($this->getNextColPos(), $row['bom_qty']);
            $this->setContentValue($this->getNextColPos(), $row['bom_v_name']);
            $this->setContentValue($this->getNextColPos(), $row['part_list_qty']);
            $this->setContentValue($this->getNextColPos(), $row['part_list_v_name']);           
        }

        $this->setTableContentBorder("A", $startRow, "I", $this->currentRow);
        
        //=============================================================
        // ONLY IN BOM
        
        
        $this->currentColumn = "A";
        $this->currentRow++; 
        $this->currentRow++; 
           
        $this->setContentValue($this->getCurrentPos(), "Only In Bom Datas", true);
        
        $this->currentColumn = "A";
        $this->currentRow++; 
                            
        $this->setResultTableHeader($this->getCurrentPos(), "UPC");
        $this->setResultTableHeader($this->getNextColPos(), "FNA");
        $this->setResultTableHeader($this->getNextColPos(), "P/No");
        $this->setResultTableHeader($this->getNextColPos(), "P/Name");        
        $this->setResultTableHeader($this->getNextColPos(), "Qty");
        $this->setResultTableHeader($this->getNextColPos(), "V.Name");
        
        $startRow = $this->currentRow+1;   
        
        foreach ($input["only_in_bom_datas"] as $row)
        {
            $this->currentColumn = "A";
            $this->currentRow++; 

            $this->setContentValue($this->getCurrentPos(), $row['upc']);
            $this->setContentValue($this->getNextColPos(), $row['fna']);
            $this->setContentValue($this->getNextColPos(), $row['p_no']);
            $this->setContentValue($this->getNextColPos(), $row['p_name']);            
            $this->setContentValue($this->getNextColPos(), $row['qty']);
            $this->setContentValue($this->getNextColPos(), $row['v_name']);        
        }

                    
        $this->setTableContentBorder("A", $startRow, "F", $this->currentRow); 
        
        
        //=============================================================
        // ONLY IN PART LIST
        
        $this->currentColumn = "A";
        $this->currentRow++; 
        $this->currentRow++; 
           
        $this->setContentValue($this->getCurrentPos(), "Only In Part List Datas", true);
        
        $this->currentColumn = "A";
        $this->currentRow++; 
                            
        $this->setResultTableHeader($this->getCurrentPos(), "UPC");
        $this->setResultTableHeader($this->getNextColPos(), "FNA");
        $this->setResultTableHeader($this->getNextColPos(), "P/No");
        $this->setResultTableHeader($this->getNextColPos(), "P/Name");         
        $this->setResultTableHeader($this->getNextColPos(), "Qty");
        $this->setResultTableHeader($this->getNextColPos(), "V.Name");
        $this->setResultTableHeader($this->getNextColPos(), "Order Type");         
        
        $startRow = $this->currentRow+1;   
        
        foreach ($input["only_in_part_list_datas"] as $row)
        {
            $this->currentColumn = "A";
            $this->currentRow++; 

            $this->setContentValue($this->getCurrentPos(), $row['upc']);
            $this->setContentValue($this->getNextColPos(), $row['fna']);
            $this->setContentValue($this->getNextColPos(), $row['p_no']);
            $this->setContentValue($this->getNextColPos(), $row['p_name']);              
            $this->setContentValue($this->getNextColPos(), $row['qty']);
            $this->setContentValue($this->getNextColPos(), $row['v_name']);        
            $this->setContentValue($this->getNextColPos(), $row['order_type']);              
        }

                    
        $this->setTableContentBorder("A", $startRow, "G", $this->currentRow);

        
        //=============================================================
        // OUTPUT
        
        
        self::export($spreadsheet, "BomCompare");

    }
    

    private function setTableContentBorder($startCol, $startRow, $endCol, $endRow) {
        if ($endRow >= $startRow) {
            $theStyle = $this->theSheet->getStyle($startCol.$startRow.":".$endCol.$endRow);        
            $theStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);   
        }
        
    }
    
    private function setResultTableHeader($pos, $value) {
        $this->setContentValue($pos, $value, true, true, "eeeeee"); 
    }
    
    private function setResultTableRow($name, $value) {
        $this->currentColumn = "A";
        $this->currentRow++; 
        
        $this->setContentValue($this->getCurrentPos(), $name);
        $this->setContentValue($this->getNextColPos(), $value);
    }

    private function setContentValue($pos, $value, $bold=false, $border=false, $bgColor=false) {
        $this->theSheet->setCellValue($pos, $value);        
        $theStyle = $this->theSheet->getStyle($pos);
        
        if ($bold) {
            $theStyle->getFont()->setBold(true); 
        }

        if ($border) {
            $theStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
        }
        
        if (!empty($bgColor)) {
           $theStyle->getFill()->setFillType(Fill::FILL_SOLID);        
           $theStyle->getFill()->getStartColor()->setRGB($bgColor);
        }

        
    }
    
    private function getWidth($name) {
        if ($name == 'base') return 9;
        if ($name == 'base2') return 12;        
        if ($name == 'large') return 15;
        if ($name == 'large2') return 19;    
        if ($name == 'large3') return 27;           
    }
    
    private function getCurrentPos() {
        return $this->currentColumn.$this->currentRow; 
    }
    
    private function getNextColPos() {
        $this->currentColumn++;
        return $this->currentColumn.$this->currentRow;       
    }
    
}
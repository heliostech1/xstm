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

class TestPerformanceExcel extends ExcelExporter
{
    
    private $theSheet = null;
    
    public function generate($input) {

        $spreadsheet = new Spreadsheet();

        $this->theSheet = $spreadsheet->getActiveSheet();

       
       //-----------------------------------------------------------------------

        $startTime = DataHelper::logTime();
        
        $currentCol = "A";
        for ($i = 1; $i <= 100; $i++) {
            $currentCol = "A";
            
            for ($j = 1; $j <= 50; $j++) {                
                $currentCol++;
                $pos = $currentCol.$i;
                
                $value = (($i % 2) == 0)? $i: ""; 
                if (!empty($value)) {
                    $this->theSheet->setCellValue($pos, $value);
                }
                

            }
            
        }
        
        $pos = "A1:Z50";
        
        $theStyle = $this->theSheet->getStyle("A1:AZ100");

          
          /* */
        $conditional1 = new Conditional();
        $conditional1->setConditionType(Conditional::CONDITION_CONTAINSBLANKS);
   //     $conditional1->setOperatorType(Conditional::OPERATOR_EQUAL);
  //      $conditional1->addCondition("");
        $conditional1->getStyle()->getFont()->getColor()->setARGB(Color::COLOR_RED);
        $conditional1->getStyle()->getFont()->setBold(true);

        //$conditional1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)
        //             ->getEndColor()->setARGB(Color::COLOR_YELLOW);
        
        
        $conditional1->getStyle()->getFill()->setFillType(Fill::FILL_SOLID)        
                     ->getEndColor()->setRGB('cccccc');  
            
            
        $conditionalStyles = $this->theSheet->getStyle($pos)->getConditionalStyles();
        $conditionalStyles[] = $conditional1;

        $this->theSheet->getStyle($pos)->setConditionalStyles($conditionalStyles);

        
                 
        $endTime = DataHelper::logTime($startTime);
        
        self::export($spreadsheet, "TestPerformance");
        
        DataHelper::logTime($endTime);
    
    }
    
}    


                /*
                $theStyle = $this->theSheet->getStyle($pos);
                
                $this->theSheet->getColumnDimension($currentCol)->setWidth(100);
                
                $theStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $theStyle->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $theStyle->getAlignment()->setIndent(3);       

                $theStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $theStyle->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $theStyle->getAlignment()->setIndent(3);       
                
                $theStyle->getFill()->setFillType(Fill::FILL_SOLID);        
                $theStyle->getFill()->getStartColor()->setRGB('1034a6');
                $theStyle->getFont()->setBold(true); 
                $theStyle->getFont()->getColor()->setRGB('FFFFFF');          
                $theStyle->getFont()->setSize(13);  

                $theStyle->getFont()->setItalic(true);
                $theStyle->getFont()->setUnderline(true); 
                 */
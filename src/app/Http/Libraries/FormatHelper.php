<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use App\Http\Libraries\FileMgr;

class FormatHelper extends MyBaseLib
{      
    public static function empty_if_null($data) {
        return (empty($data))? "": $data;
    }
    //********************************************************
    //
    // PART: COMMON  ( PRICE, TIME, DISTANCE, ...)
    //
    //*********************************************************
    
    public static function wrapInfoDiv($message) {
        return  "<div >".$message."</div>";
    }

    public static function wrapErrorDiv($message) {
        return  "<div >".$message."</div>";
    }

    public static function wrapErrorsDiv($messageList) {
        $output = array();
        foreach ($messageList as $data) {
            $output[] = self::wrapErrorDiv($data);
        }
        return implode(' ', $output);
    }
    
    public static function wrapSuccessDiv($message) {
        return  "<div class='success'>".$message."</div>";
    }
    
    
    public static function get_error_message($rule, $label1 = "", $label2 = "", $label3 = "", $label4 = "") {
        $template = "";//static::ci->lang->line($rule);
        $message = sprintf($template, $label1, $label2, $label3, $label4);
        
        return  "<div >".$message."</div>";
    }
        
    public static function get_error($label, $rule) {
      $message = "";//static::ci->lang->line($rule);
      $message = str_replace("%s", $label, $message);
      return  $message;
    }
    
    public static function convert_num_to_yes_no($data) {
        if (is_null($data) || $data === "") {
            return "";
        }
        return ($data)? "yes":"no";        
    }
    
   static function padZero($number, $length=2) {
        while (strlen($number) < $length) {
            $number = '0'.$number;
        }
        return $number;
    }
        
    /*
   static function truncate($string, $length, $stopanywhere=true, $delimiter = "...") {
        //truncates a string to a certain char length, stopping on a word if not specified otherwise.
        if (strlen($string) > $length) {
            //limit hit!
            $string = substr($string,0,($length -3));
            if ($stopanywhere) {
                //stop anywhere
                $string .= $delimiter;
            } else{
                //stop on a word.
                $string = substr($string,0,strrpos($string,' ')).$delimiter;
            }
        }
        return $string;
    }

*/
    static function truncate_by_mb_strcut($string, $length, $stopanywhere=true, $delimiter = "...") {
        mb_internal_encoding('UTF-8');
        
        if (strlen($string) > $length) {
            $string = mb_strcut($string,0,($length -3));
            if ($stopanywhere) {
                $string .= $delimiter;
            } else{
                $string = substr($string,0,strrpos($string,' ')).$delimiter;
            }
        }
        return $string;
    }
    
    static function truncate($string, $length, $stopanywhere=true, $delimiter = "...") {
        mb_internal_encoding('UTF-8');
        $del_len = strlen($delimiter);
        
        if (mb_strlen($string) > $length) {
            $string = mb_substr($string,0,($length - $del_len));
            $string = trim($string);
             
            if ($stopanywhere) {
                $string .= $delimiter;
            } else{
                $string = mb_substr($string,0,mb_strrpos($string,' ')).$delimiter;
            }
        }
        return $string;
    }
        
    /* aaa,bbb, ccc  ->  aaa, bbb, ccc */
   static function format_list_string($data) {
        $data_arr =  explode(",", $data);
        
        if ($data_arr && sizeof($data_arr) > 0) {
            foreach($data_arr as &$str) {
               $str = trim($str);
            }
            return implode(", ", $data_arr);
        }
        return $data;
        
    }
   
    /* aaa,bbb,ccc  ->  aaa,..  */
   static function brief_list_string($data) {
        $data_arr =  explode(",", $data);
        $first = "";
        
        foreach($data_arr as $str) {
            if (!empty($str)) {
                if (empty($first)) {
                    $first = $str;
                }
                else {
                    return "$first,..";
                }
            }
        }

        return $first;
        
    }
        
   static function format_title_for_html_element($data) {
        if (empty($data)) return $data;
        
        $data = static::convert_br_to_newline($data);
        return htmlspecialchars($data);
    }
    
   static function convert_br_to_newline($data) {
        return  static::convert_br_to($data, "\r\n");
    }
    
   static function convert_br_to($data, $change_to) {
        if (empty($data)) return $data;
        
        $breaks = array("<br />","<br>","<br/>");  
        return  str_ireplace($breaks, $change_to, $data);  
    }
    
   static function formatNumber($value, $decimal = 2) {
        if (DataHelper::isEmptyNotZero($value)) return "";
        $value = self::removeComma($value);
        
        return number_format($value , $decimal, '.' , ',');
    }           
    

    
    // 8000=8,000 ; 1234.1 = 1,234.1
   static function formatNumberActual($value, $decimal=2) {
        if (!is_numeric($value)) return "";
        
        $output = number_format($value , $decimal, '.' , ',');        
        return static::remove_decimal_trailing_zero($output);   
    }

    static function formatDecimal($value, $decimal = 2) {
        if (DataHelper::isEmptyNotZero($value)) return "";
        $value = self::removeComma($value);
        if (!is_numeric($value)) return "";
        
        return number_format($value , $decimal, '.' , '');
    }
    
    // 0.02=0.02; 4.001=4.00; 8.488 =8.49
    static function formatDecimalLimit($value, $limit = 2) {
        if (DataHelper::isEmptyNotZero($value)) return "";
    
        $parts = explode(".", $value);
        if (isset($parts[1]) && strlen($parts[1]) > $limit) {
            return number_format($value , $limit, '.' , '');
        }
        return $value;
    }
    
    // 0.02=0.02; 4.000=4; 80=80
   static function formatDecimalActual($value, $limit=2) {
        $num = static::formatDecimalLimit($value, $limit);
        return static::remove_decimal_trailing_zero($num);
    }
    
   static function remove_decimal_trailing_zero($value) {
        if (empty($value) && $value !== 0) return "";
        
        $pos = strpos($value, '.');        
        if ($pos === false) { // it is integer number
            return $value;
        }
        else { // it is decimal number
            return rtrim(rtrim($value, '0'), '.'); // remove trailing zero
        }
        
    }
        
   static function parseNumber($value, $default = null) {
        if ($value !== 0 && empty($value)) {
            return (is_null($default))? null: $default;  
        }      

        $value = DataHelper::trim($value);
        $value = str_replace(",", "", $value);
        
        if (DataHelper::isInteger($value)) {
            return intval($value);
        }
        else if (DataHelper::isDecimal($value)) {
            return doubleval($value);
        }
        else if (!is_null($default)) {
            return $default;
        }
        
        return $value;
    }    

   static function round_formatted_price($value) {
        $value = static::parseNumber($value);
        if (!empty($value)) {
            $value = round($value);
        }  
        return static::formatNumberActual($value);
    }
    
   static function format_speed($value) {
        if (is_null($value)) return "";
        return number_format($value , 2, '.' , ',');
    }
    
   static function formatPrice($value) {
        if ($value !== 0 && $value !== "0" && empty($value)) return "";
        return number_format($value , 2, '.' , ',');
    }    

   static function formatWeight($value) {
        if ($value !== 0 && $value !== "0" && empty($value)) return "";
        return number_format($value , 2, '.' , ',');
    }   

   static function formatQuantity($value) {
        if ($value !== 0 && $value !== "0" && empty($value)) return "";
        
        if (is_numeric($value)) {
            return static::add_comma( floatval($value) );
        }
        return $value;
    }   

    
   static function format_minute($sec) {
        if (empty($sec)) return "";
        return DateHelper::secondToMinute($sec);
    }
    
    
   static function format_duration($sec, $padHours = false, $show_second = false) 
    {
        $hms = "";
        $hours = intval(intval($sec) / 3600); 
        $hms .= ($padHours) 
              ? str_pad($hours, 2, "0", STR_PAD_LEFT)
              : $hours;
        
        $minutes = intval(($sec / 60) % 60); 
        
        $hms .= ":".str_pad($minutes, 2, "0", STR_PAD_LEFT);

        if ($show_second) {
            $seconds = intval($sec % 60); 
            $hms .= ":".str_pad($seconds, 2, "0", STR_PAD_LEFT);
        }

        return $hms;
    }
        
   static function format_duration_from_range($start_sql_time, $finish_sql_time) {
        if (is_null($start_sql_time) || is_null($finish_sql_time) ) {
            return "";
        }
        
        $start = strtotime($start_sql_time);
        $finish = strtotime($finish_sql_time);
        
        if ($start && $finish) {
            return static::format_duration($finish - $start);
        }
        return "";
    }
    
   static function format_distance($distance_km) {
        if (is_null($distance_km) ) {
            return "";
        }
        return number_format($distance_km , 2, '.' , ',');
    } 
        
   static function format_distance_from_range($start_odo_km, $finish_odo_km) {
        if (is_null($start_odo_km) || is_null($finish_odo_km) ) {
            return "";
        }
        
        $distance = $finish_odo_km - $start_odo_km;
        return number_format($distance , 2, '.' , ',');
    } 
        
   static function format_time_to_thai_range($start_time, $finish_time) {   
        $str =  DateHelper::timeToThai($start_time)." - ";
        
        if (DateHelper::is_time_same_date($start_time, $finish_time)) {
            $str .= DateHelper::timeToThai($finish_time, true, true); 
        }
        else {
            $str .= DateHelper::timeToThai($finish_time);
        }

        return $str;
    }
    
   static function format_sql_to_thai_range($start_time, $finish_time) {   
        $str =  DateHelper::sql_to_thai($start_time)." - ";
        
        if (DateHelper::is_sql_same_date($start_time, $finish_time)) {
            $str .= DateHelper::sql_to_thai($finish_time, true, true); 
        }
        else {
            $str .= DateHelper::sql_to_thai($finish_time);
        }

        return $str;
    }    
        
    public static function format_geopoint($lat, $lon) {
        if (!empty($lat)) {
           return static::formatDecimalLimit($lat, 5)."/". static::formatDecimalLimit($lon, 5);
        }
        return "";
    }
        
    //for report 
   static function getCriteriaDateRangeString($criDate, $criToDate, $incTime=false, $criTime=null, $criToTime=null, $isLimitToDate=true) { 
        $str = "";
        $criDate = DataHelper::trim($criDate);
        $criToDate = DataHelper::trim($criToDate);
        
        if ($incTime) {
            $criTime = ($criTime)? " ".$criTime: " 00:00";
            $criToTime = ($criToTime)? " ".$criToTime: " 23:59";
            $criToTime = (empty($criToDate))? " ".date('H:i'): $criToTime;
        }
        else {
            $criTime = "";
            $criToTime = "";
        }
        //------------------------------------
        
        if (empty($criDate) && empty($criToDate)) {
            return "-"; 
        }

        if (!$incTime) {
            if ( ($criDate == $criToDate) ||  (DateHelper::isThaiToday($criDate) && empty($criToDate)) ) {
                return $criDate; 
            }
        }

        $noToDateString = "ไม่ระบุ"; //($isLimitToDate)? DateHelper::nowThai().$criToTime: "ไม่ระบุ";
        
        $str = (!empty($criDate))? $criDate.$criTime: "ไม่ระบุ";
        $str .= " - ";
        $str .= (!empty($criToDate))? $criToDate.$criToTime: $noToDateString; 

        return $str;
    }    
    
   static function getCriteriaDateThaiRangeString($criDate, $criToDate) { 
        $str = "";
        $criDate = DataHelper::trim($criDate);
        $criToDate = DataHelper::trim($criToDate);
        
        if (empty($criDate) && empty($criToDate)) {
            return "-"; 
        }

        $criDateTh = DateHelper::thaiToMonthThai($criDate);
        $criToDateTh = DateHelper::thaiToMonthThai($criToDate);
        
        if ( ($criDate == $criToDate) ||  (DateHelper::isThaiToday($criDate) && empty($criToDate)) ) {
              return $criDateTh; 
         }    

        $str = (!empty($criDate))? $criDateTh: "ไม่กำหนด";
        $str .= " - ";
        $str .= (!empty($criToDate))? $criToDateTh: DateHelper::thaiToMonthThai( DateHelper::nowThai() ); 

        return $str;
    }   
       
   static function removeComma($value) {
        if ($value !== 0 && empty($value)) return null;  
        return  str_replace(",", "", $value);
    }
    
   static function remove_comma($value) {
        if ($value !== 0 && empty($value)) return null;  
        return  str_replace(",", "", $value);
    }
    
   static function remove_dash($value) {
        if ($value !== 0 && empty($value)) return null;  
        return  str_replace("-", "", $value);
    }
    
   static function remove_space_dash($value) {
        if ($value !== 0 && empty($value)) return null;  
        $value =  str_replace("-", "", $value);
        $value =  str_replace(" ", "", $value);
        return $value;
    }
    
   static function add_comma($value) {
        if ($value !== 0 && empty($value)) return null;  
        $value = static::remove_comma($value);
        $parts = explode(".", $value);
        $decimal = isset($parts[1])? ".".$parts[1]: "";
        return number_format($parts[0]).$decimal;        
    }

    
    static function isGid($value) {
        if (empty($value)) return false;
         
        $value = static::parseGid($value);
        
        if (preg_match('/^[a-zA-Z\d]{20}$/i', $value)) {
            return true;
        }
        return false;
    }
    
    static function parseGid($value) {
        if (empty($value)) return $value;     
        
        $value = trim($value);
        $parts = explode('/', $value);
        $output = $parts[sizeof($parts) -1];
        return $output;        
    }
    
    /**
     * Indents a flat JSON string to make it more human-readable.
     * @param string $json The original JSON string to process.
     * @return string Indented version of the original JSON string.
     */
   static function format_json($json) {
    
        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = '&nbsp;';
        $newLine     = "<br/>";
        $prevChar    = '';
        $outOfQuotes = true;
    
        for ($i=0; $i<=$strLen; $i++) {
            $char = substr($json, $i, 1);

            if ($char == '"' && $prevChar != '\\') {
                $outOfQuotes = !$outOfQuotes;
            
            } else if(($char == '}' || $char == ']') && $outOfQuotes) {
                $result .= $newLine;
                $pos --;
                for ($j=0; $j<$pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $result .= $char;
            if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' || $char == '[') {
                    $pos ++;
                }
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }
            
            $prevChar = $char;
        }

        return $result;
    }    
    
    
   static function add_bracket($data) {
        if (!empty($data)) {
            return "($data)";
        }
        return "";
    }
            
   static function tis620_to_utf8($data) {
        if (empty($data)) return "";
        return  @iconv("tis-620", "utf-8//IGNORE", $data);
    }
    
    
   static function remove_newline($string) {
        if (empty($string)) return "";
        return preg_replace('/\s+/', ' ', trim($string));        
    }
        
   
    static function arrayToCsvLine($values) {
        $line = '';

        $values = array_map(function ($v) {
            return '"' . str_replace('"', '""', $v) . '"';
        }, $values);

        $line .= implode(',', $values);

        return $line;
    }

   static function priceToThaiText($number) {
       $number = self::parseNumber($number, 0);
       
       $number = number_format($number, 2, '.', '');
       $numberx = $number;
       $txtnum1 = array('ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า','สิบ');
       $txtnum2 = array('','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน');
       $number = str_replace(",","",$number);
       $number = str_replace(" ","",$number);
       $number = str_replace("บาท","",$number);
       $number = explode(".",$number);
       if(sizeof($number)>2){
           return 'ทศนิยมหลายตัว';
           exit;
       }
       $strlen = strlen($number[0]);
       $convert = '';
       for($i=0;$i<$strlen;$i++){
           $n = substr($number[0], $i,1);
           if($n!=0){
               if($i==($strlen-1) AND $n==1){ $convert .= 'เอ็ด'; }
               elseif($i==($strlen-2) AND $n==2){  $convert .= 'ยี่'; }
               elseif($i==($strlen-2) AND $n==1){ $convert .= ''; }
               else{ $convert .= $txtnum1[$n]; }
               $convert .= $txtnum2[$strlen-$i-1];
           }
       }
   
       $convert .= 'บาท';
       if($number[1]=='0' OR $number[1]=='00' OR
               $number[1]==''){
                   $convert .= 'ถ้วน';
       }else{
           $strlen = strlen($number[1]);
           for($i=0;$i<$strlen;$i++){
               $n = substr($number[1], $i,1);
               if($n!=0){
                   if($i==($strlen-1) AND $n==1){$convert
                   .= 'เอ็ด';}
                   elseif($i==($strlen-2) AND
                           $n==2){$convert .= 'ยี่';}
                           elseif($i==($strlen-2) AND
                                   $n==1){$convert .= '';}
                                   else{ $convert .= $txtnum1[$n];}
                                   $convert .= $txtnum2[$strlen-$i-1];
               }
           }
           $convert .= 'สตางค์';
       }
       //แก้ต่ำกว่า 1 บาท ให้แสดงคำว่าศูนย์ แก้ ศูนย์บาท
       if($numberx < 1)
       {
           $convert = "ศูนย์" .  $convert;
       }
   
       //แก้เอ็ดสตางค์
       $len = strlen($numberx);
       $lendot1 = $len - 2;
       $lendot2 = $len - 1;
       if(($numberx[$lendot1] == 0) && ($numberx[$lendot2] == 1))
       {
           $convert = substr($convert,0,-10);
           $convert = $convert . "หนึ่งสตางค์";
       }
   
       //แก้เอ็ดบาท สำหรับค่า 1-1.99
       if($numberx >= 1)
       {
           if($numberx < 2)
           {
               $convert = substr($convert,4);
               $convert = "หนึ่ง" .  $convert;
           }
       }
       return $convert;
   }  
   
   static function getPriceNoVat($price) {
       $vatPercent  = 7;
       $price = self::parseNumber($price, 0);
       
       $ret = (100/ ( 100 + $vatPercent) )*$price;
       
       return $ret;
       
   }
   
    /** 
     * http://herethere.net/~samson/php/color_gradient/
     * getPalerColor('4D318F')
     */
   static function getNeighborColor($color, $dark = false) {
        if (empty($color)) return;

        $theColorBegin =   hexdec($color);
        $theColorEnd  = ($dark)? 0x000000 : 0xffffff; // white

        $theR0 = ($theColorBegin & 0xff0000) >> 16;
        $theG0 = ($theColorBegin & 0x00ff00) >> 8;
        $theB0 = ($theColorBegin & 0x0000ff) >> 0;

        $theR1 = ($theColorEnd & 0xff0000) >> 16;
        $theG1 = ($theColorEnd & 0x00ff00) >> 8;
        $theB1 = ($theColorEnd & 0x0000ff) >> 0;

        $i = ($dark)? 4: 3;
        $theNumSteps = 10;

        $theR = static::interpolate($theR0, $theR1, $i, $theNumSteps);
        $theG = static::interpolate($theG0, $theG1, $i, $theNumSteps);
        $theB = static::interpolate($theB0, $theB1, $i, $theNumSteps);

        $theVal = ((($theR << 8) | $theG) << 8) | $theB;

        $hexVal = sprintf("%06X", $theVal);

        return $hexVal;
    }
    
    // return the interpolated value between pBegin and pEnd
   static function interpolate($pBegin, $pEnd, $pStep, $pMax) {
        if ($pBegin < $pEnd) {
            return (($pEnd - $pBegin) * ($pStep / $pMax)) + $pBegin;
        } else {
            return (($pBegin - $pEnd) * (1 - ($pStep / $pMax))) + $pEnd;
        }
    }  

   static function formatImageLink($image) {
       if (empty($image)) return "";
       
       $output = "<a href='../fileUpload/view?name=$image' target='_blank'>$image</a>";      
               
       return $output;
    }
           
   static function formatImageLinkSimple($image) {
       if (empty($image)) return "";
       
       $items = DataHelper::stringToArray($image);
       
       $output = array();
       foreach ($items as $item) {
           $output[] = "<a href='../fileUpload/view?name=$item' target='_blank'>เรียกดู</a>"; 
       }   
               
       return DataHelper::arrayToString($output);
    }
    
    public static function getImageHtml($image) {
        if (empty($image)) return "";
        
        $viewFileUrl = url('/fileUpload/view'); //"../fileUpload/view";
                
        $thumbSrc = $viewFileUrl."?name=".$image."&thumb=true";         
        $fileLink = $viewFileUrl."?name=".$image;
        
        $output = "<div><a style='text-decoration:none' href='" . $fileLink ."' target='_blank' >" .
            "<img height='100' width='120'  src='". $thumbSrc ."' />" .      
            "</a></div>";     
        return $output;
        
    }
    
    public static function getImageHtmlForReport($image, $value) {
        if (empty($image)) return $value;
        
        $viewFileUrl = url('/fileUpload/view'); //"../fileUpload/view";
                
        $thumbSrc = $viewFileUrl."?name=".$image."&thumb=true&fast=true";   
        $fileLink = $viewFileUrl."?name=".$image;
        
       // $thumbSrc =  FileMgr::getPredictThumbFilePath($image).$image;
        
        $output = "<div><a style='text-decoration:none' href='" . $fileLink ."' target='_blank' >" .
            "<img height='80' width='90'  src='". $thumbSrc ."' />" .      
            "</a></div><div>$value</div>";     
        return $output;
        
    }
    
    public static function getImageLinkForPdf( $image) {
        if (empty($image)) return "";
        $result = FileMgr::getPredictThumbFilePath($image);
  
       //  myDebug($result);
        return $result;
    }
    
    /** UNUSED NOW */
    public static function getImageHtmlForPdf($appToken, $image, $value = "") {
        if (empty($image)) return $value;
       
        
        
        $viewFileUrl =  url('/commonService/viewImage');
        $thumbSrc = $viewFileUrl."?name=".$image."&thumb=Y&token=".$appToken;       
        
       // $base64Src = self::getBase64ImageSrc($image, $thumbSrc);
       // myDebug($base64Src);
        $output = "<img height='80' width='90'  src='". $thumbSrc ."' />";    
        
        if (!empty($value)) {
            $output .= "<br/>$value";
        }
        
        return $output;
        
    }       
}






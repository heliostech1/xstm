<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use Carbon\Carbon;
use App\Http\Models\Rdb;

class DateHelper extends MyBaseLib
{
    //***********************************************************************************
    //
    // PART: CONVERT DATE FORMAT
    //
    //***********************************************************************************

    /*
     * input: unix timestamp
     * output: sql-string ('Y-m-d H:i:s')
     */
    static function timeToSql($time, $inc_time = true) {
        if ($inc_time) {
            return date( 'Y-m-d H:i:s', $time );
        }
        else {
            return date( 'Y-m-d', $time );
        }

    }


    /*
     * input: unix timestamp
     * output: th-string ('d/m/Y H:i:s')
     */
    static function timeToThai($time, $inc_time = true, $only_time = false)
    {
        if (empty($time)) return "";
        
        $year = date('Y', $time) + 543;
        $r  = date('d', $time).'/'.date('m', $time).'/'.$year;
        if ($inc_time) {
            $time_part = date('H', $time).':'.date('i', $time).':'.date('s', $time);
            if ($only_time) {
                return $time_part;
            }
            else {
                $r .= ' '.$time_part;
            }
        }

        return $r;
    }

    /*
     * input: unix timestamp
     * output: th-string ('d M Y')
     */
    static function timeToThaiFull($time)
    {        
        $day = DataHelper::toInteger( date('d', $time) );
        $year = date('Y', $time) + 543;
        $m = date('m', $time);
        $m = DataHelper::toInteger($m);
        $fullMonth = Rdb::getMonth( strval($m) );
        
        $r  = $day.' '.$fullMonth.' '.$year;
        return $r;
    }
    
    /*
     * input:  th-string ('d/m/Y')
     * output: unix timestamp
     */
    static function thaiToTime($thai, $time_str = null) {
        $sql = static::thaiToSql($thai);
        if (!empty($time_str)) {
            $sql = $sql." ".$time_str;
        }
        return static::sqlToTime($sql);
    }

    // กรณ๊เป็นวันที่เป็นวันนี้ ไม่ต้องแสดงวัน แสดงเฉพาะ เวลา
    static function sqlToThai_no_date_if_today($str)
    {
        $time = static::sqlToTime($str);

        if (static::is_today($time)) {
            return date('H', $time).':'.date('i', $time).':'.date('s', $time);
        }
        else {
            return static::timeToThai($time, true);
        }
    }


    /*
     * input: sql-string ('Y-m-d H:i:s')
     * output:  unix timestamp
     */
    static function sqlToTime($str) {
        return strtotime( $str );
    }


    static function thaiToSqlMonth($thai) {
        $sql = static::thaiToSql($thai);
        return self::sqlToSqlMonth($sql);
    }
    
    static function sqlToSqlMonth($sql) {
        if (empty($sql)) return "";
        return substr($sql, 0, 7);
    }
    
    static function mongoDateToSqlMonth($mongo) {
        $sqlDate = self::mongoDateToSql($mongo);
        return self::sqlToSqlMonth($sqlDate);
    }
    
    static function thaiToThaiMonth($thai) {
        if (empty($thai)) return "";
        return substr($thai, 3, 8);
    }
    
    //**************************************************************

    /*
     * input: sql-string ('Y-m-d H:i:s')
     * output:  milisecond unix timestamp
     */
    static function sqlToMilisec($str, $mili_sec_part) {
        //if (strlen($mili_sec_part) != 3) return false
        return strtotime( $str ).$mili_sec_part;
    }

    static function milisecToSql ($mili_sec) {
        $timestamp = substr($mili_sec, 0, -3);
        return static::timeToSql($timestamp);
    }

    static function milisecToThai ($mili_sec) {
        $timestamp = substr($mili_sec, 0, -3);
        return static::timeToThai($timestamp);
    }

    //**************************************************************


    /*
     * input: sql-string ('Y-m-d H:i:s')
     * output: thai-string ('d/m/Y H:i:s')
     */
    static function sqlToThai($str, $inc_time = true, $only_time = false) {
        if (empty($str)) return "";

        $time = static::sqlToTime($str);
        return static::timeToThai($time, $inc_time, $only_time);

    }

    static function sqlMonthToThaiMonth($str) {
        if (empty($str)) return "";

        $year = substr($str, 0, 4);
        $month = substr($str, 5, 2);
        
        $thaiYear =  DataHelper::toInteger($year) + 543;
           
        return $month."/".$thaiYear;

    }    
    
    //**************************************************************


    /**
     * input:  thai-string ('d/m/Y')
     * output: sql-string ('Y-m-d')
     */

    static function thaiToSql($datestr = '', $inc_time = false)
    {
        if ($datestr == '') {
            return FALSE;
        }

        $datestr = trim($datestr);
        $timestr = "";

        if ($inc_time) {
            if ( ! static::isValidThai($datestr)){
                return FALSE;
            }

            $parts = explode(" ", $datestr);
            $datestr = $parts[0];
            $timestr = (sizeof($parts) > 0)? " ".$parts[1]: "";
        }
        else {
            if ( ! static::isValidThaiDate($datestr)){
                return FALSE;
            }
        }

        $ex = explode("/", $datestr);

        $day   = (strlen($ex['0']) == 1) ? '0'.$ex['0']  : $ex['0'];
        $month = (strlen($ex['1']) == 1) ? '0'.$ex['1']  : $ex['1'];
        $year  = (strlen($ex['2']) == 2) ? '25'.$ex['2'] : $ex['2'];
        $year = $year - 543;

        return $year."-".$month."-".$day.$timestr;
        //return mktime($hour, $min, $sec, $month, $day, $year);
    }

    /** 29/09/2020 = >   29/09/2563 */
    static function thaiChristYearToThai($dateStr, $default = null)
    {
        if (empty($dateStr) || !self::isValidThaiChristYear($dateStr)) {
            return $default;
        }

        $dateStr = trim($dateStr);

        $ex = explode("/", $dateStr);

        $day   = (strlen($ex['0']) == 1) ? '0'.$ex['0']  : $ex['0'];
        $month = (strlen($ex['1']) == 1) ? '0'.$ex['1']  : $ex['1'];
        $year  = (strlen($ex['2']) == 2) ? '20'.$ex['2'] : $ex['2'];
        $year = $year + 543;

        return $day."/".$month."/".$year;
    }
    
    
    /** 29/09/63 = >   29/09/2563 */
    static function thaiShortYearToThai($dateStr, $default = null)
    {
        if (empty($dateStr) || !self::isValidThaiShortYear($dateStr)) {
            return $default;
        }

        $dateStr = trim($dateStr);
        $currYear = self::todayThaiYear();
                
        $ex = explode("/", $dateStr);

        $day   = (strlen($ex['0']) == 1) ? '0'.$ex['0']  : $ex['0'];
        $month = (strlen($ex['1']) == 1) ? '0'.$ex['1']  : $ex['1'];
        $year  = substr($currYear, 0, 2) . $ex['2'];
        

        return $day."/".$month."/".$year;
    }
    
    
    /** 'Y-m-d H:i:s' => 'Y-m-d' */
    static function getSqlDatePart($sql) {
        if (empty($sql)) return "";

        $parts = explode(" ", $sql);
        return $parts[0];
    }

    /** 'Y-m-d H:i:s' => 'H:i:s */
    static function getSqlTimePart($sql) {
        if (empty($sql)) return "";

        $parts = explode(" ", $sql);
        return (sizeof($parts)>1)? $parts[1]: "";
    }
    
    /** 'Y-m-d => 'd' */    
    static function getSqlDayPart($sql) {
        if (empty($sql)) return "";

        return substr($sql, 8, 2);
    }



    /** Ymd => Y-m-d */
    static function addDashToSqlDate($sql) {
        if (empty($sql) || strlen($sql) != 8) return "";

        return substr($sql, 0, 4)."-".substr($sql, 4, 2)."-".substr($sql, 6, 2);
    }

    /** ex 21/01/2560 => 21 ม.ค.  2560 */
    static function thaiToMonthThai($thai) {
        if ( ! static::isValidThaiDate($thai)){
            return $thai;
        }
        
        $ex = explode("/", $thai);        
        $day   = (strlen($ex['0']) == 1) ? '0'.$ex['0']  : $ex['0'];
        $month = (strlen($ex['1']) == 1) ? '0'.$ex['1']  : $ex['1'];
        $year  = (strlen($ex['2']) == 2) ? '25'.$ex['2'] : $ex['2'];        
       
        $monthIndex = intval($month) - 1;        
        $monthList = ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
                 
        return $day." ".$monthList[$monthIndex]." ".$year;
    }
    
    //***********************************************************************************
    //
    // PART: PLAY WITH TODAY
    //
    //***********************************************************************************

    static function nowMongo() {      
        return MongoHelper::date();
    }

    static function todayMongo() {
        $date = self::nowSql(false)." 00:00:00";
        $timestamp = static::sqlToTime($date);

        return MongoHelper::date($timestamp);
    }
    
    /**
     *  วันที่และเวลาปัจจุบัน ในรูปแบบ  ('d/m/Y H:i:s')
     */
    static function nowThai($inc_time = false) {
        return static::timeToThai( time() , $inc_time);
    }


    /**
     *  วันที่และเวลาปัจจุบัน ในรูปแบบ  ('Y-m-d H:i:s')
     */
    static function nowSql($inc_time = true) {
        return static::timeToSql(static::now(), $inc_time);
    }

    /**
     *  วันที่แปัจจุบัน ในรูปแบบ  ('d/m/Y')
     */
    static function todayThai() {
        return static::timeToThai(static::now(), false);
    }

    static function todayThaiYear() {
        return  date('Y', static::now()) + 543;
    }



    /**
     *  วันที่แปัจจุบัน ในรูปแบบ  ('Y-m-d')
     */
    static function todaySql() {
        return static::timeToSql(static::now(), false);
    }

    static function todaySqlMonth() {
        $todaySql = self::todaySql();
        return  self::sqlToSqlMonth($todaySql);
    }
    
    static function todayDayPart() {
        $todaySql = self::todaySql();
        return  self::getSqlDayPart($todaySql);
    }

    /**
     *  ปีไทย 57,58,...
     */
    static function todayThaiShortYear() {
        $year = date('Y', static::now()) + 543;
        return substr($year, 2, 2);
    }

    /**
     *  ปี คศ  18,19,...
     */
    static function todaySqlShortYear() {
        $year = date('Y', static::now());
        return substr($year, 2, 2);
    }
    
    /**
     *  input:  timestamp
     *  ตรวจสอบว่า input เป็นเวลาในวันปัจจุบันหรือไม่
     */
    static function isToday($time) {
        $today_str = static::timeToSql(static::now(), false);
        $time_str = static::timeToSql($time, false);

        return ($time_str == $today_str);
    }

    /**
     *  input: thai-string ('d/m/Y')
     *  ตรวจสอบว่า input เป็นเวลาในวันปัจจุบันหรือไม่
     */
    static function isThaiToday($thai) {
        return (static::nowThai() == $thai);
    }

    /**
     *  input: sql-string ('Y-m-d H:i:s')
     *  ตรวจสอบว่า input เป็นเวลาในวันปัจจุบันหรือไม่
     */
    static function isSqlToday($sql) {
        if (empty($sql)) return false;

        return static::is_today(strtotime($sql));
    }

    static function getBesideSqlDate($sqlDate, $numberDays) {
        if (empty($sqlDate) || empty($numberDays)) return null;
        
        $time = strtotime($sqlDate);
        $besideTime = (24*60*60) * ($numberDays);        
        $resultTime = $time + $besideTime;
        
        return self::timeToSql($resultTime, false);
        
    }    
    
    static function getBesideThaiDate($thaiDate, $numberDays) {
        if (empty($thaiDate) || empty($numberDays)) return null;
        
        $time = self::thaiToTime($thaiDate);
        $besideTime = (24*60*60) * ($numberDays);        
        $resultTime = $time + $besideTime;
        
        return self::timeToThai($resultTime, false);
        
    }  
    
    //***********************************************************************************
    //
    // PART: CONVERT SECOND MINUTE HOUR DAY
    //
    //***********************************************************************************


    static function second_to_minute($sec) {
        if (empty($sec)) return 0;
        return intval($sec / 60);
    }

    static function second_to_hour_minute($sec) {
        if (empty($sec)) {
            return array(0,0);
        }
        return array( floor($sec / 3600), floor(($sec / 60) % 60));
    }

    static function hour_minute_to_second($hour, $minute) {
        $hour = (empty($hour))? 0: $hour;
        $minute = (empty($minute))? 0: $minute;
        return ($hour*60*60) + ($minute*60);
    }

    static function second_to_day_with_unit($sec, $only_day=false,  $only_unit_sec=false) {
        //var_dump($sec);

        if (empty($sec)) return "";

        //----------------------------------
        $month = 2592000;
        if ( $sec % $month === 0 && intval($sec / $month) <= 11) {
            if ($only_day) return intval($sec / $month);
            if ($only_unit_sec) return $month;
            return intval($sec / $month). " เดือน";
        }

        //----------------------------------
        $week = 604800;
        if ( $sec % $week === 0 && intval($sec / $week) <= 3) {
            if ($only_day) return intval($sec / $week);
            if ($only_unit_sec) return $week;
            return intval($sec / $week). " สัปดาห์";
        }

        //----------------------------------
        $units = array( /*2592000 => 'เดือน', 604800 => 'สัปดาห์',*/ 86400 => 'วัน', 3600 => 'ชั่วโมง', 60 => 'นาที', 1 => 'วินาที');
        $result = array();
        foreach($units as $divisor => $unitName) {
            $units = intval($sec / $divisor);
            if ($units) {
                $sec %= $divisor;
                $result[] = "$units $unitName";

                if ($only_day) return $units;
                if ($only_unit_sec) return $divisor;
            }
        }

        return implode(', ', $result);
    }

    public static function get_different_thai_date($date1, $date2) {
        $timestamp1 = static::thaiToTime($date1);
        $timestamp2 = static::thaiToTime($date2);

        return abs($timestamp2 - $timestamp1)/(24*60*60);
    }

    //***********************************************************************************
    //
    // PART: VALIDATE TIME
    //
    //***********************************************************************************

    /**
     *  input: timestamp)
     *  ตรวจสอบว่า input เป็นเวลาที่อยู่ในวันเดียวกันหรือไม่
     */
    static function isTimeSameDate($time1, $time2) {
        $time1_str = static::timeToSql($time1, false);
        $time2_str = static::timeToSql($time2, false);

        return ($time1_str == $time2_str);
    }

    /**
     *  input: sql-string ('Y-m-d H:i:s')
     *  ตรวจสอบว่า input เป็นเวลาที่อยู่ในวันเดียวกันหรือไม่
     */
    static function isSqlSameDate($str1, $str2) {
        return static::isTimeSameDate(static::sqlToTime($str1), static::sqlToTime($str2));
    }

    /**
     *  input: timestamp
     *  ตรวจสอบว่า input เป็น timestamp ที่ถูกต้องหรือไม่
     */
    static function isValidTime($time) {
        return ( !empty($time) && is_numeric($time) && (int)$time == $time );
    }

    static function isSqlFormat($str) {
        if (preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',$str)){
            return true;
        }
        return false;
    }

    static function isValidSql($str) {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $str, $matches)) {
            if (checkdate($matches[2], $matches[3], $matches[1])) {
                return true;
            }
        }

        return false;
    }


    static function isValidThai($dateTime) {
        if (preg_match("/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4} ([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/", $dateTime, $matches))  {
            return true;
        }
        return false;
    }

    static function isValidSqlDate($str)
    {
        return (bool)preg_match('/^(\d{4})-(\d{2})-(\d{2})$/i', $str);
    }

    static function isValidThaiDate($str) {
        if (preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/i', $str)) {
            $parts = explode("/", $str);
            if (intval($parts[0]) > 31) return false;
            if (intval($parts[1]) > 12) return false;
            if (intval($parts[2]) < 2500) return false;

            return true;
        }

        return false;
    }
    
    static function isValidThaiChristYear($str) {
        if (preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/i', $str)) {
            $parts = explode("/", $str);
            if (intval($parts[0]) > 31) return false;
            if (intval($parts[1]) > 12) return false;
            if (intval($parts[2]) < 2000) return false;

            return true;
        }

        return false;
    }    

    static function isValidThaiShortYear($str) {
        if (preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{2}$/i', $str)) {
            $parts = explode("/", $str);
            if (intval($parts[0]) > 31) return false;
            if (intval($parts[1]) > 12) return false;
            //if (intval($parts[2]) < 2000) return false;

            return true;
        }

        return false;
    } 
    
    static function isValidTimestamp($timestamp) {
        return ((string) (int) $timestamp === $timestamp) 
            && ($timestamp <= PHP_INT_MAX)
            && ($timestamp >= ~PHP_INT_MAX);
    }

    
    //***********************************************************************************
    //
    // PART: .............
    //
    //***********************************************************************************


    static function formatSqlDate($str) {
        if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/",$str)){ // date match
            return "$str 00:00:00";
        }
        else if (preg_match("/^([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/",$str)) { // time match
            return static::today_sql()." $str";
        }
        else if ($str == "today") {
            return static::today_sql()." 00:00:00";
        }
        else if ($str == "yesterday") {
            return static::yesterday_sql()." 00:00:00";
        }
        else {
            return $str;
        }
    }


    /**
     *  input: sql-string ('Y-m-d H:i:s'),  second
     *  ตรวจสอบว่าเวลารวมของ  input1 + input2 < เวลาปัจจุบันหรือไม่  ( timeout แล้วหรือไม่ )
     */
    static function isTimeout($str, $second) {
        $time = static::sqlToTime($str);
        $now = time();
        if ($now > ($time + $second)) {
            return true;
        }
        return false;
    }

    /**
     *  input: sql-string ('Y-m-d H:i:s')
     *  ตรวจสอบว่า input เป็นเวลาที่น้อยกว่าเวลาปัจจุบันหรือไม่  ( น้อยกว่า = หมดอายุ = return true)
     */
    static function isExpire($str) {
        return (time() > static::sqlToTime($str));
    }


    /**
     * แปลงจากเลขวินาที เป็น ข้อมูลเวลามีหน่วย  เช่น 120 -> 2 นาที
     */
    static function secondsToUnit($secs)
    {
        $units = array("สัปดาห์" => 7*24*3600, "วัน" => 24*3600, "ชั่วโมง" => 3600, "นาที" => 60, "วินาที" => 1);

        if ( $secs == 0 ) return "0 วินาที";
        $s = "";

        foreach ( $units as $name => $divisor ) {
            if ( $quot = intval($secs / $divisor) ) {
                $s .= "$quot $name";
                $s .= ", ";
                $secs -= $quot * $divisor;
            }
        }

        return substr($s, 0, -2);
    }

    //============================================
    // MONGO


    static function mongoDateToThai($date, $incTime = true) {
        if (empty($date) || is_string($date) || !method_exists($date, 'toDateTime')) return $date;

        //static::ci->data_helper->debug_object($date);
        $output =  static::timeToThai($date->toDateTime()->getTimestamp(), $incTime);
        return $output;
    }

    static function mongoDateToThaiFull($date) {
        if (empty($date) || is_string($date) || !method_exists($date, 'toDateTime')) return $date;
        $output =  static::timeToThaiFull($date->toDateTime()->getTimestamp());
        return $output;
    }
    
    static function mongoDateToSql($date, $incTime = true) {
        if (empty($date) || is_string($date) || !method_exists($date, 'toDateTime')) return $date;

        $output =  static::timeToSql($date->toDateTime()->getTimestamp(), $incTime);
        return $output;
    }

    static function mongoDateToTimestamp($date) {
        if (empty($date) || is_string($date) || !method_exists($date, 'toDateTime')) return $date;

        $output =  $date->toDateTime()->getTimestamp();
        return $output;
    }    
    /**
     * input:  th-string ('d/m/Y') ex 08/06/2560
     * output: MongoDate
     */
    static function thaiToMongoDate($date, $time="") {
        if (empty($date) || !is_string($date) || !static::isDateThai($date)) return $date;

        $time = (empty($time))? "00:00:00": $time;
        $timestamp = static::thaiToTime($date, $time);

        return MongoHelper::date($timestamp);
    }

    
    static function thaiTimeToMongoDate($datetime) {
       if (empty($datetime)) return $datetime;
      
       $pieces = explode(" ", trim($datetime) );
       $datePart = (sizeof($pieces) > 0)? $pieces[0]: "";
       $timePart = (sizeof($pieces) > 1)? $pieces[1]: "";   
       
       return self::thaiToMongoDate($datePart, $timePart);
    }
    
    /**
     * input:  sql-string ('y-m-d') ex 2017-01-01
     * output: MongoDate
     */
    static function sqlToMongoDate($date, $time="") {
        if (empty($date) || !is_string($date) || !static::isValidSqlDate($date)) return $date;

        $time = (empty($time))? "00:00:00": $time;        
        $date = $date." ".$time;

        $timestamp = static::sqlToTime($date);

        return MongoHelper::date($timestamp);
    }
    

    static function timestampToMongoDate($timestamp) {
        return MongoHelper::date($timestamp);
    }
    
    /**
     * input:  th-string ('d/m/Y') ex 08/06/2560
     * output: MongoDate
     */
    static function thaiToMongoDateRange($date, $toDate) {
        $output = array(false, false);
        
        if (empty($date) && empty($toDate)) return $output;
        if (!empty($date) && ( !is_string($date)  || !static::isDateThai($date) ) ) return $output;
        if (!empty($toDate) && ( !is_string($toDate)  || !static::isDateThai($toDate) ) ) return $output;        
        
        $date1 = (!empty($date))? MongoHelper::date( DateHelper::thaiToTime($date, "00:00:00") ): false;
        $date2 = (!empty($toDate))? MongoHelper::date( DateHelper::thaiToTime($toDate, "23:59:59") ): false;
        
        $output = array($date1, $date2);    
        return $output;
    }
    
    
    static function sqlToMongoDateRange($date, $toDate) {
        $output = array(false, false);
        
        if (empty($date) && empty($toDate)) return $output;
        if (!empty($date) && ( !is_string($date)  || !static::isValidSqlDate($date) ) ) return $output;
        if (!empty($toDate) && ( !is_string($toDate)  || !static::isValidSqlDate($toDate) ) ) return $output;        
        
        $date1 = (!empty($date))? MongoHelper::date( DateHelper::sqlToTime("$date 00:00:00") ): false;
        $date2 = (!empty($toDate))? MongoHelper::date( DateHelper::sqlToTime("$toDate 23:59:59") ): false;
        
        $output = array($date1, $date2);    
        return $output;
    }
    
    
    //==========================================

    static function now()
    {
        if (false) {// if (strtolower($CI->config->item('time_reference')) == 'gmt')
            $now = time();
            $system_time = mktime(gmdate("H", $now), gmdate("i", $now), gmdate("s", $now), gmdate("m", $now), gmdate("d", $now), gmdate("Y", $now));

            if (strlen($system_time) < 10)
            {
                $system_time = time();
                //log_message('error', 'The Date class could not set a proper GMT timestamp so the local time() value was used.');
            }

            return $system_time;
        }
        else {
            return time();
        }
    }
    
    static function sqlToThaiListString($str) {
        $list = DataHelper::stringToArray($str);
        $output = array();
        foreach ($list as $data) {
            $output[] = self::sqlToThai($data, false);
        }
        return DataHelper::arrayToString($output);
    }
        
    //=============================================
    // VALIDATE

    static function isDateThai($str)
    {
        if (preg_match('/^[0-9]{1,2}\/[0-9]{1,2}\/[0-9]{4}$/i', $str)) {
            $parts = explode("/", $str);
            if (intval($parts[0]) > 31) return false;
            if (intval($parts[1]) > 12) return false;
            if (intval($parts[2]) < 2500) return false;

            return true;
        }

        return false;
    }


    
}


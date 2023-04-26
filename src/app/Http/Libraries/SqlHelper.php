<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use DB;

class SqlHelper extends MyBaseLib
{          
    public static function  escapeStr($data) {
        return $data;
        //return DB::connection()->getPdo()->quote($data);        
    }
    
    public static function appendWhere($where, $statement) {
        $where = static::_addPrefixWhere($where);
        $where .= $statement;
        return $where;
    }

    public static function appendWhereLike($where, $column, $data) {
        $data = static::_trim($data);

        if (!empty($data)) {
            $where = static::_addPrefixWhere($where);
            $where .= $column." LIKE '%".static::escapeStr($data)."%' ";
        }
        return $where;
    }

    public static function appendWhereEqual($where, $column, $data) {

        $data = static::_trim($data);
         
        if (!empty($data)) {
            $where = static::_addPrefixWhere($where);
            $where .= $column." = '".static::escapeStr($data)."' ";
        }
        return $where;
    }

    public static function appendWhereNotEqual($where, $column, $data) {

        $data = static::_trim($data);
         
        if (!empty($data)) {
            $where = static::_addPrefixWhere($where);
            $where .= $column." != '".static::escapeStr($data)."' ";
        }
        return $where;
    }

    public static function appendWhereNotEmpty($where, $column) {
        $where = static::_addPrefixWhere($where);
        $where .= $column." != '' ";
        return $where;
    }

    public static function appendWhere_not_null_and_not_empty($where, $column) {
        $where = static::_addPrefixWhere($where);
        $where .= "( ( ".$column." IS NOT NULL ) AND ( ".$column." != '' ) )";
        return $where;
    }

    public static function appendWhere_null_or_empty($where, $column) {
        $where = static::_addPrefixWhere($where);
        $where .= "( ( ".$column." IS NULL ) OR ( ".$column." = '' ) )";
        return $where;
    }

    public static function appendWhere_false($where, $column) {
        $where = static::_addPrefixWhere($where);
        $where .= $column." = 'false' ";
        return $where;
    }

    public static function appendWhere_yes_no($where, $column, $data) {
        $data = static::_trim($data);
         
        if (!empty($data)) {
            $nData = ($data == 'yes')? 1: 0;
            $where = static::_addPrefixWhere($where);
            $where .= $column." = $nData ";
        }
        return $where;
    }

    public static function appendWhere_or_like_column_array($where, $column_arr, $data) {
         
        if (empty($data)) {
            return $where;
        }

        $where = static::_addPrefixWhere($where);
        $where .= " ( ";
        $first = true;

        foreach ($column_arr as &$value) {
            if (!$first) {
                $where .= " OR ";
            }
            $first = false;
            $where .= $value." LIKE '%".static::escapeStr($data)."%' ";
        }

        $where .= " ) ";
        return $where;
    }

    public static function appendWhere_or_equal_array($where, $column, $data_arr) {
        return static::_appendWhereInArray($where, $column, $data_arr, true);
    }

    public static function appendWhereInArray($where, $column, $data_arr) {
        return static::_appendWhereInArray($where, $column, $data_arr, true);
    }

    public static function appendWhere_not_in_array($where, $column, $data_arr) {
        return static::_appendWhereInArray($where, $column, $data_arr, false);
    }

    private static function _appendWhereInArray($where, $column, $data_arr, $in = true) {

        $data_arr = DataHelper::string_to_array_clear_empty($data_arr);

        if (sizeof($data_arr) <= 0) {
            return $where;
        }

        $in_cmd = ($in)? "IN": "NOT IN";
        $where = static::_addPrefixWhere($where);
        $where .= " $column $in_cmd (";

        $first = true;
        foreach ($data_arr as &$value) {
            if (!$first) {
                $where .= " , ";
            }
            $first = false;
            $where .= " '".static::escapeStr($value)."' ";
        }

        $where .= " ) ";

        return $where;
    }

    /**
     * @param String date      thai format ex. "17/01/2556"
     * @param String to_date   thai format ex. "17/01/2556"
     */
    public static function appendWhere_date_range($where, $column, $date, $to_date, $inc_time = true, $time=null, $to_time=null) {
        if (empty($date) && empty($to_date)) {
            return $where;
        }

        $date = DateHelper::thai_to_sql($date);
        $to_date = DateHelper::thai_to_sql($to_date);

        if ($inc_time) {
            $time = ($time)? $time.":00": "00:00:00";
            $to_time = ($to_time)? $to_time.":59": "23:59:59";

            $date = (!empty($date))? $date." ".$time: $date;
            $to_date  = (!empty($to_date))? $to_date." ".$to_time: $to_date;
        }

        $where = static::_addPrefixWhere($where);

        if ($date && $to_date) {
            $where .= " ( ".$column." BETWEEN '".$date."' AND '".$to_date."' ) ";
        }
        else if ($date) {
            $where .= " ( ".$column." >= '".$date."' )";
        }
        else if ($to_date) {
            $where .= " ( ".$column." <= '".$to_date."' )";
        }

        //echo $where;
        return $where;
    }

    /**
     * @param String date      thai format ex. "17/01/2556"
     * @param String to_date   thai format ex. "17/01/2556"
     * column eventdata is integer 13 length
     */
    public static function appendWhere_eventdata_date_range($where, $column, $date, $to_date, $inc_time = true, $time=null, $to_time=null) {
        if (empty($date) && empty($to_date)) {
            return $where;
        }

        $date = DateHelper::thai_to_sql($date);
        $to_date = DateHelper::thai_to_sql($to_date);

        if ($inc_time) {
            $time = ($time)? $time.":00": "00:00:00";
            $to_time = ($to_time)? $to_time.":59": "23:59:59";

            $date = (!empty($date))? strtotime($date." ".$time)."000": $date;
            $to_date  = (!empty($to_date))? strtotime($to_date." ".$to_time)."000": $to_date;
        }

        $where = static::_addPrefixWhere($where);

        if ($date && $to_date) {
            $where .= " ( ".$column." BETWEEN ".$date." AND ".$to_date." ) ";
        }
        else if ($date) {
            $where .= " ( ".$column." >= ".$date." )";
        }
        else if ($to_date) {
            $where .= " ( ".$column." <= ".$to_date." )";
        }

        //echo $where;
        return $where;
    }

    //=================================================================

    public static function appendWhere_geocode($where, $lat, $lon, $radius) {

        $lat = static::_trim($lat);
        $lon = static::_trim($lon);
        $radius = static::_trim($radius);

        if (empty($lat) || empty($lon) || empty($radius) ) {
            return $where;
        }
         
        $offset = MapHelper::get_geo_offset($lat, $lon, $radius);
        //$message = "LAT:".$offset['lat']." ---- LON:".$offset['lon'];

        $where = static::_addPrefixWhere($where);
        $where .= " ( latitude >= ".($lat - $offset['lat'])." AND ";
        $where .= "  latitude <= ".($lat + $offset['lat'])." AND ";
        $where .= "  longitude >= ".($lon - $offset['lon'])." AND ";
        $where .= "  longitude <= ".($lon + $offset['lon'])." ) ";

        return $where;
    }


    public static function appendWhere_tm_status($where, $column, $data) {

        if ($data == Rdb::tm_status_all_active) {
            return static::appendWhere_or_equal_array(
                    $where, $column, Rdb::tm_status_all_active_array());
        }
        else if ($data == Rdb::tm_status_on_delivery) {
            return static::appendWhere_or_equal_array(
                    $where, $column, Rdb::tm_status_on_delivery_array());
        }
        else {
            return static::appendWhere_equal($where, $column, $data);
        }

    }

    public static function appendWhere_shipment_status($where, $column, $data) {

        if ($data == Rdb::shipment_status_all_active) {
            return static::appendWhere_or_equal_array(
                    $where, $column, Rdb::shipment_status_all_active_array());
        }
        else {
            return static::appendWhere_equal($where, $column, $data);
        }

    }

    public static function appendWhere_job_status($where, $column, $data) {
        if ($data == Rdb::job_statusProcessing) {
            return static::appendWhere_or_equal_array(
                    $where, $column,
                    array(Rdb::job_statusProcessing,
                            Rdb::job_status_error )
                    );
        }
        else {
            return static::appendWhere_equal($where, $column, $data);
        }

    }

    //*******************************************************************

    public static function _addPrefixWhere($where) {
        if ( $where == "" ) {
            $where = "WHERE ";
        }
        else{
            $where .= " AND ";
        }
        return $where;
    }

    public static function _trim($data) {
        $data = (!is_null($data))? trim($data): $data;
        return $data;
    }

}
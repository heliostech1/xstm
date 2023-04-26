<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use DB;

/*
 * https://github.com/jenssegers/laravel-mongodb
 */
class MongoHelper extends MyBaseLib
{  
    public static function getObjectId($id) {
        if (empty($id)) return "";
    
        $objectId = "";
        try {
            $objectId = new \MongoDB\BSON\ObjectID($id);
        } catch (\MongoDB\Driver\Exception\InvalidArgumentException $e) {
    
        }
        return $objectId ;
    }
    
    public static function getIdByObject($objectId) {
        if (empty($objectId)) return "";
    
        return $objectId->__toString();
        //return $objectId->{'_id'};
    }
    
    
    //===========================================================
    
    public static function date($stamp = FALSE)
    {
        if ( $stamp == FALSE ) {
            $stamp = time();
        }
    
        $stamp = $stamp*1000;
    
        return new \MongoDB\BSON\UTCDateTime("$stamp"); //String milliseconds
    }
    
    public static function appendWhere($where, $column, $data) {
        $data = static::_trim($data);
    
        if (!empty($data)) {
            $where[] = array( "cmd"=>"where", "params"=> [$column, $data]);
        }
        return $where;
    }
    
    public static function appendWhereNot($where, $column, $data) {
        $data = static::_trim($data);
    
        if (!empty($data)) {
            $where[] = array( "cmd"=>"where", "params"=> [$column,  "!=", $data]);
        }
        return $where;
    }
        
    public static function appendWhereSign($where, $column, $sign, $data) {
        //$data = static::_trim($data); // trime 0 แล้วได้ '0'

        if (!empty($data) || $data === 0) {
            $where[] = array( "cmd"=>"where", "params"=> [$column, $sign, $data]);
        }
        return $where;
    }    
    
    public static function appendWhereInt($where, $column, $data) {
        $data = static::_trim($data);
    
        if (!empty($data)) {
            $data =  DataHelper::toInteger($data);
            $where[] = array( "cmd"=>"where", "params"=> [$column, $data]);
        }
        return $where;
    }
    
    public static function appendWhereLike($where, $column, $data) {
        $data = static::_trim($data);
    
        if (!empty($data)) {
            $where[] = array( "cmd"=>"where", "params"=> [$column, "like",   '%'. $data.'%']);
        }
        return $where;
    }
    
    public static function appendWhereNull($where, $column ) {
        //$data = static::_trim($data);
    
        //if (!empty($data)) {
            $where[] = array( "cmd"=>"where", "params"=> [$column, null]);
        //}
        return $where;
    }
    
    public static function appendWhereNotNull($where, $column ) {

        $where[] = array( "cmd"=>"where", "params"=> [$column, "!=", null]);
  
        return $where;
    }
    
    
    public static function appendWhereDate($where, $column, $date) {
        if (empty($date)) {
            return $where;
        }
        
        $date = DateHelper::thaiToSql($date);
        $date = self::date( DateHelper::sqlToTime($date) );
        
        
        $where[] = array( "cmd"=>"where", "params"=> [$column, '=', $date] );
        return $where; 

    }
    
    
    public static function appendWhereDateRange($where, $column, $date, $toDate, $incTime = true, $time=null, $toTime=null) {
        if (empty($date) && empty($toDate)) {
            return $where;
        }
    
        $date = DateHelper::thaiToSql($date);
        $toDate = DateHelper::thaiToSql($toDate);
    
        if ($incTime) {
            $time = ($time)? $time.":00": "00:00:00";
            $toTime = ($toTime)? $toTime.":59": "23:59:59";
            $date = (!empty($date))? $date." ".$time: $date;
            $toDate  = (!empty($toDate))? $toDate." ".$toTime: $toDate;
        }
    
        $date1 = (!empty($date))? self::date( DateHelper::sqlToTime($date) ) : null;
        $date2 = (!empty($toDate))? self::date( DateHelper::sqlToTime($toDate) ): null;
    
        //DataHelper::debug($date1);
        if (!empty($date1) && !empty($date2)) {
            $where[] = array( "cmd"=>"whereBetween", "params"=> [$column, [ $date1, $date2 ]] );
        }
        else if (!empty($date1)) {
            $where[] = array( "cmd"=>"where", "params"=> [$column, '>=', $date1] );
        }
        else if (!empty($date2)) {
            $where[] = array( "cmd"=>"where", "params"=> [$column, '<=', $date2] );
        }
    
        return $where;
    }
    
    public static function appendWhereMongoId($where, $column, $data) {
        $data = static::_trim($data);
        $objectId = MongoHelper::getObjectId( $data );
    
        if (!empty($objectId)) {
            $where[] = array( "cmd"=>"where", "params"=> [$column, $data]);
        }
        else if (empty($objectId) && !empty($data)) {
            $where[] = array( "cmd"=>"where", "params"=> [$column,  MongoHelper::getObjectId( "000000000000000000000000" )]);
        }
        return $where;
    }
    
    //* where .....AND ( xxx = "aaa" OR  yyy = "bbb") 
    public static function appendWhereGroupLike($where, $columnList, $data) {
        $data = static::_trim($data);
        
        if (!empty($data)) {
            $where[] = array( "type" => "groupLike", "columns"=> $columnList, "value"=> $data);
        }
        return $where;
    }
    
    public static function appendWhereGroupSomeNotNull($where, $columnList) {
        $where[] = array( "type" => "groupSomeNotNull", "columns"=> $columnList);
        return $where;
    }
    
    public static function appendWhereIn($where, $column, $dataArrays ) {
        //$data = static::_trim($data);
    
        //if (!empty($data)) {
            $where[] = array( "cmd"=>"whereIn", "params"=> [$column, $dataArrays ]);
        //}
        return $where;
    }    
    
    public static function _trim($data) {
        $data = (!is_null($data))? trim($data): $data;
        return $data;
    }
}        


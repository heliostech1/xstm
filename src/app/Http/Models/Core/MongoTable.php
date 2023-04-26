<?php

namespace App\Http\Models\Core;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\SqlHelper;
use App\Http\Libraries\DataHelper;

class MongoTable extends MyBaseModel
{

    //https://github.com/jenssegers/laravel-mongodb
    //https://docs.mongodb.com/v3.0/reference/method/db.collection.find/
    
    public static function getOutput($sTable, $columns, $options=array()) {

        $where = isset($options['where'])? $options['where']: "";
        $totalWhere = isset($options['totalWhere'])? $options['totalWhere']: "";
        $dftOrder = isset($options['dftOrder'])? $options['dftOrder']: null;
        $displayTotal = isset($options['displayTotal'])? $options['displayTotal']: true;
        $isSort = isset($options['isSort'])? $options['isSort']: true;
        $connection = isset($options['connection'])? $options['connection']: null;
        $sortByConvert = isset($options['sortByConvert'])? $options['sortByConvert']: null;
        
        //=======================================================================================
        // Count
        
        $iFilteredTotal = self::getTotalCount($sTable, $where, $connection);
        
        if ($displayTotal) {
            $iTotal = self::getTotalCount($sTable, $totalWhere, $connection); 
        }
        else {
            $iTotal = $iFilteredTotal;
        }
        
        //=======================================================================================
        // Prepair

        $query = (!empty($connection))? $connection->table($sTable) : DB::table($sTable);         
        $query = self::applyQueryWhere($query, $where);
                
        $query->select($columns);
        
        // =======================================================================================
        // Paging
         
        
        if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' ) {
            
            $query->skip(  intval( $_POST['iDisplayStart'] )  ); // skip
            $query->take(  intval( $_POST['iDisplayLength'] ) ); // limit 
        }

        // =======================================================================================
        // Ordering
         
        $sortFields = array();
        if ( isset($_POST['iSortCol_0']) && $isSort) {
            
            for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ ) {
                
                $sort_col_number = intval($_POST['iSortCol_'.$i]);
                                
                if ( $_POST[ 'bSortable_'.$sort_col_number ] == 'true' ) {
                    
                    $field = $_POST['mDataProp_'.$sort_col_number];                    
                    $order = $_POST['sSortDir_'.$i];
                    
                    $convertField = getMyProp($sortByConvert, $field, '');
                    $convertField = !empty($convertField)? $convertField: $field;
                    
                    //myDebug($convertField);
                    $sortFields[] = array($convertField, $order);
                }
            }
        }
        
        //DataHelper::debug($sortFields);
        
        foreach ($sortFields as $sortField) {
            $query->orderBy($sortField[0], $sortField[1]);  // !!! order by จุดที่ทำให้ช้ามากๆ 
        }
        
        if (sizeof($sortFields) <= 0 && !empty($dftOrder) && $isSort) {
            $query->orderBy($dftOrder[0], $dftOrder[1]);
        }

        $query->orderBy("_id", "asc"); 
        
        
        // =======================================================================================
        // Query
    
        $rResult = $query->get();

        // =======================================================================================
        // Output
         
        
        $counter = (isset( $_POST['iDisplayStart'] ))? $_POST['iDisplayStart'] + 1: 1;
        $finalResult = array();
        
        foreach ($rResult as &$aRow)
        {
            $aRow['mongoId'] = $aRow['_id']; // $this->mongo_helper->get_id_by_mongoId_object($aRow['_id']);
            $aRow['counterColumn'] = $counter++;            
        
            foreach ($columns as $column) {
                if (!isset($aRow[$column])) {
                    $aRow[$column] = "";
                }
            }
            
            $finalResult[] = $aRow;
        }
                
        $output = array(
            "sEcho" => (!empty($_POST['sEcho']))? intval($_POST['sEcho']) : 0,
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => $finalResult
        );
        
        return $output;
    }

    
    public static function getOutputNoPaging($sTable, $columns, $options=array()) {

        $where = isset($options['where'])? $options['where']: "";
        $totalWhere = isset($options['totalWhere'])? $options['totalWhere']: "";
        $dftOrder = isset($options['dftOrder'])? $options['dftOrder']: null;
        $displayTotal = isset($options['displayTotal'])? $options['displayTotal']: true;
        $isSort = isset($options['isSort'])? $options['isSort']: true;
        $connection = isset($options['connection'])? $options['connection']: null;
        
        
        //=======================================================================================
        // Prepair

        $query = (!empty($connection))? $connection->table($sTable) : DB::table($sTable);         
        $query = self::applyQueryWhere($query, $where);
                
        $query->select($columns);
        

        // =======================================================================================
        // Ordering         
        
        if (!empty($dftOrder) && $isSort) {
            $query->orderBy($dftOrder[0], $dftOrder[1]);
        }

        // =======================================================================================
        // Query
    
        $rResult = $query->get();

        // =======================================================================================
        // Output
         
        
        $counter = 1;
        $finalResult = array();
        
        foreach ($rResult as &$aRow)
        {
            $aRow['mongoId'] = $aRow['_id']; // $this->mongo_helper->get_id_by_mongoId_object($aRow['_id']);
            $aRow['counterColumn'] = $counter++;            
        
            foreach ($columns as $column) {
                if (!isset($aRow[$column])) {
                    $aRow[$column] = "";
                }
            }
            
            $finalResult[] = $aRow;
        }
                
        return $finalResult;
    }    
    
    //=======================================================================
    
    private static function getTotalCount($table, $where, $connection) {
        
        $query = (!empty($connection))? $connection->table($table) : DB::table($table);
        $query = self::applyQueryWhere($query, $where);        
        $count = $query->count();
        
        return $count;
    }
    
    
    // https://github.com/jenssegers/laravel-mongodb
    // ใช้คู่กับ MongoHelper
    private static function applyQueryWhere($query, $where) {
        
        if (!empty($where) && sizeof($where) > 0) {
            foreach ($where as $data) {
                
                if (isset($data['cmd'])) {
                    $cmd = $data['cmd'];

                    //DataHelper::debug($data);
                    if (sizeof($data['params']) == 3) {                        
                        $query->$cmd($data['params'][0], $data['params'][1], $data['params'][2]);
                    }
                    else if (sizeof($data['params']) == 2) {
                        $query->$cmd($data['params'][0], $data['params'][1]);
                    }
                }
                
                if (isset($data['type']) && $data['type'] == "groupLike") {
                     $value = $data['value'];
                     $columns  = $data['columns'];
                     
                     $query->where( function($query) use ($columns, $value) {       
                        $query->where($columns[0], "like", '%'. $value.'%');        
                        $query->orWhere($columns[1], "like", '%'. $value.'%');
                        //if (sizeof($columns) > 2) { $query->orWhere($columns[2], "like", '%'. $value.'%');}                        
                     } );
     
                }

                if (isset($data['type']) && $data['type'] == "groupSomeNotNull") {
       
                     $columns  = $data['columns'];
                     
                     $query->where( function($query) use ($columns) {       
                        $query->where($columns[0], "!=", null);        
                        $query->orWhere($columns[1], "!=", null);
                        if (sizeof($columns) > 2) { $query->orWhere($columns[2], "!=", null); }  
                        if (sizeof($columns) > 3) { $query->orWhere($columns[3], "!=", null); }     
                        if (sizeof($columns) > 4) { $query->orWhere($columns[4], "!=", null); }     
                     } );
     
                }
                
            }
        }      
        return $query;
    }
    
   //====================================
        
    public static function getEmptyOutput() {
        $echo = (!empty($_POST['sEcho']))? intval($_POST['sEcho']) : 0;
        
        $output = array(
            "sEcho" => $echo,
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => array()
        );
        
        return $output;
    }
    

}



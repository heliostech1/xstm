<?php

namespace App\Http\Models\Core;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\SqlHelper;
use App\Http\Libraries\DataHelper;

class DataTable extends MyBaseModel
{

    public static function getOutput($sTable, $sIndexColumn, $aColumns, $where="", $dft_order="", $total_where = "", $group_by = "", $dft_limit = "") {

        // =======================================================================================
        /*
         * Paging
         */
        $sLimit = "";
        if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
        {
            $sLimit = "LIMIT ".SqlHelper::escapeStr( $_POST['iDisplayStart'] ).", ".
            SqlHelper::escapeStr( $_POST['iDisplayLength'] );
        }

        // =======================================================================================
        /*
         * Ordering
         */
        
        $additional_order = "";
        $prefix_order = "";
        
        if (is_array($dft_order)) {
            $additional_order = (sizeof($dft_order)>1)? $dft_order[1]: "";
            $prefix_order = (sizeof($dft_order)>2)? $dft_order[2]: "";
            $dft_order = $dft_order[0];
        }        
        
        $sOrder = "";
        $colOrder = "";
        $dirOrder = "ASC";
        
        if ( isset( $_POST['iSortCol_0'] ) )
        {
            $sOrder = "ORDER BY  ";
            
            if (!empty($prefix_order)) {
                $sOrder .= $prefix_order.", ";
            }
                        
            for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
            {
                $sortColNum = intval($_POST['iSortCol_'.$i]);
                
                if ( $_POST[ 'bSortable_'.$sortColNum ] == "true" )
                {
                    $colOrder = $_POST['mDataProp_'.$sortColNum];
                    /*
                    $aColumn = $aColumns[ $sortColNum ];
                    $colOrder = (is_array($aColumn)? $aColumn[1]: $aColumn); //แบบ1 array("stop.name","stop_name"),แบบ2 "stop_id"
                    */
                    
                    $dirOrder = SqlHelper::escapeStr( $_POST['sSortDir_'.$i] );
                    $sOrder .= $colOrder." ".$dirOrder.", ";
                }
            }
                        
            if (!empty($additional_order)) {
                $sOrder .= $additional_order.", ";
            }
            
            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                $sOrder = "";
            }
        }       
        
        if (empty($sOrder) && !empty($dft_order)) {
            $sOrder = $dft_order;
        }
        
        if (empty($sLimit) && !empty($dft_limit)) {
            $sLimit = $dft_limit;
        }
        // =======================================================================================
        /*
         * SQL queries
         * Get data to display
         */
        
        // จัดการกรณีชื่อ column ซ้ำกันเช่นมีทั้ง inbox.content, outbox.content 
        // ที่  $aColumns ให้ใส่มาเป็น inbox@content จะแปลงเป็น  "inbox.content as inbox@content"
        
        $aColumnsForSelect = array();
        for ( $i=0 ; $i<count($aColumns) ; $i++ ){
            if (is_array($aColumns[$i])) {
                $aColumnsForSelect[] = $aColumns[$i][0]." AS ".$aColumns[$i][1];
            }
            else if (strpos($aColumns[$i],'_DOT_') !== false) {
                $aColumnsForSelect[] = str_replace("_DOT_",".",$aColumns[$i])." AS ".$aColumns[$i];
            }
            else {
                $aColumnsForSelect[] = $aColumns[$i];
            }
        }
        
        //------------------------------------

        $sSelect = "SELECT  ".str_replace(" , ", " ", implode(", ", $aColumnsForSelect)); // SQL_CALC_FOUND_ROWS
        
        if (is_array($sTable)) {
            $join_statement = $sTable[1];
            $sTable = $sTable[0];
            $sFrom = "FROM $sTable $join_statement ";
        }
        else {
            $sFrom = "FROM $sTable  ";
        }

         $sQuery = "$sSelect $sFrom $where $group_by $sOrder $sLimit";
         
         //DataHelper::debug("DATATABLE QUERY: $sQuery");

         $rResult = DB::select( DB::raw($sQuery) );
        
         //DataHelper::debug($rResult, "RESULT:");
        // =======================================================================================
        /* Data set length after filtering */
        $rResultFilterTotal = DB::select( DB::raw( "SELECT COUNT(".$sIndexColumn.") AS result $sFrom $where "));        
        $iFilteredTotal = $rResultFilterTotal[0]->result;

        // =======================================================================================
         
        /* Total data set length */
        
        $count_total_query = "SELECT COUNT(".$sIndexColumn.") AS result  $sFrom  $total_where ";                
        //$rResultTotal = mysql_query( "SELECT COUNT(".$sIndexColumn.") FROM   $sTable  $total_where");
        $rResultTotal = DB::select( DB::raw( $count_total_query ));
        $iTotal = $rResultTotal[0]->result;
        
        //log_message("error", "DATATABLE QUERY TOTAL: $count_total_query");
        
        // =======================================================================================
        /*
         * Output
         */
        $output = array(
        "sEcho" => (!empty($_POST['sEcho']))? intval($_POST['sEcho']) : 0,
        "iTotalRecords" => $iTotal,
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
        );
         
        $counter = (isset( $_POST['iDisplayStart'] ))? $_POST['iDisplayStart'] + 1: 1;
        
        foreach ($rResult as $aRow) {
            $row = array();
        
            for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
                //แบบ1 array("stop.name","stop_name"), แบบ2 "stop_id"
                $aColumn = (is_array($aColumns[$i])? $aColumns[$i][1] : $aColumns[$i]); // เป็นกรณี เช่นใส่เป็น array(sum(count),total_count)
                if ( $aColumn != ' ' )
                {
                    $column_idx = static::remove_prefix($aColumn);
                    $row[$column_idx] = $aRow->$column_idx;
                }
            }
            
            $row['counterColumn'] = $counter++;            
            $output['aaData'][] = $row;
        }
        
        return $output;
    }

    /**  เช่น "shipment.customer_id"  แปลงเป็น "customer_id"  */
    public static function remove_prefix($col) {
        
        $dot_pos = strrpos($col, ".");
        if ($dot_pos !== FALSE) {
            $col = substr($col, $dot_pos + 1); 
        }
        return $col;
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



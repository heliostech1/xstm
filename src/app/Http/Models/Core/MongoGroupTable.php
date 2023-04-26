<?php

namespace App\Http\Models\Core;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\SqlHelper;
use App\Http\Libraries\DataHelper;

class MongoGroupTable extends MyBaseModel
{
    
    // https://docs.mongodb.com/php-library/master/reference/
    // https://docs.mongodb.com/manual/reference/sql-comparison/
    
    public static function getOutput($sTable, $columns, $options=array()) {

        $where = isset($options['where'])? $options['where']: "";
        $totalWhere = isset($options['totalWhere'])? $options['totalWhere']: "";
        $dftOrder = isset($options['dftOrder'])? $options['dftOrder']: null;
        $displayTotal = isset($options['displayTotal'])? $options['displayTotal']: true;
        $isSort = isset($options['isSort'])? $options['isSort']: true;
        $connection = isset($options['connection'])? $options['connection']: null;
        $groupBy = isset($options['groupBy'])? $options['groupBy']: null;
        $sum = isset($options['sum'])? $options['sum']: null;
        
 
        $db = DB::getMongoDB();        
        $collection = $db->selectCollection($sTable);

        //=======================================================
        // PIPELINE 
        /*
        $pl_project = array(
             '$project' => array(
                    'product_id' => 1, 
                    'product_code' => 1, 
                    'product_name' => 1,
                    'qty_remain' => 1,
            ),
        );
        */
        
        $pl_group = array(
            '$group' => array(
                '_id' => array('product_id' => '$product_id'),
                'qty_remain' => array('$sum' => '$qty_remain' ),
                'product_id' => array('$first' => '$product_id'),                        
                'product_code' => array('$first' => '$product_code'),
                'product_name' => array('$first' => '$product_name'),
            ),
        );
        
        $pl_match = array(
            '$match' => array(
                '_id' => array('product_id' => '$product_id'),

            ) 
        );
        
        $pl_sort = array(
            '$sort' => array(
                 "product_code"=> 1 
            )              
        );

        $pipeline = array($pl_match, $pl_group, $pl_sort);
        $option = array(
           "useCursor" => true,
        );
        
        $rResult = $collection->aggregate($pipeline, $option);
        
        
        // =======================================================================================
        // Query


        // =======================================================================================
        // Output
         
        
        $counter = (isset( $_POST['iDisplayStart'] ))? $_POST['iDisplayStart'] + 1: 1;
        $retDatas = array();
        
        foreach ($rResult as $aRow)
        {
            //DataHelper::debug($aRow);

            $retData = array();
            $retData['counterColumn'] = $counter++;
            
            foreach ($columns as $column) {
                $retData[$column] = isset($aRow[$column])? $aRow[$column]:"";
            }
            $retDatas[] = $retData;
        }
                
        $output = array(
            "sEcho" => (!empty($_POST['sEcho']))? intval($_POST['sEcho']) : 0,
            "iTotalRecords" => 10,
            "iTotalDisplayRecords" => 10,
            "aaData" => $retDatas
        );
        
        return $output;
    }

    
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
                $cmd = $data['cmd'];
 
                //DataHelper::debug($data);
                if (sizeof($data['params']) == 3) {                        
                    $query->$cmd($data['params'][0], $data['params'][1], $data['params'][2]);
                }
                else if (sizeof($data['params']) == 2) {
                    $query->$cmd($data['params'][0], $data['params'][1]);
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



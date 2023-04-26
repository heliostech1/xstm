<?php

namespace App\Http\Models;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Models\Core\DataTable;
use App\Http\Libraries\SqlHelper;
use App\Http\Libraries\DataHelper;
use App\Http\Models\Rdb;
use App\Http\Models\Core\MongoTable;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Libraries\FormatHelper;

class SiteUsageHistory extends MyBaseModel
{
    
    public static function getDataTable($request) {
        $criId = $request->input('criteria_id');
        $criDate = $request->input('criteria_date');
        $criToDate = $request->input('criteria_to_date');
        $criType = $request->input('criteria_type');
        
        $where = array();
        $totalWhere = array();
        $message = "";
    
        //DataHelper::debug($criDatas);
        $where = MongoHelper::appendWhere($where, 'accountId', static::getLoginAccountId());
    
        $totalWhere = $where;
        
        if (self::checkValidThaiDate($criDate, "วันที่ (จาก)") &&
            self::checkValidThaiDate($criToDate, "วันที่ (ถึง)")
        ) {
            $where = MongoHelper::appendWhereDateRange($where, 'usageTime', $criDate, $criToDate);
        }
                
        $where = MongoHelper::appendWhere($where, 'usageBy', $criId);
        $where = MongoHelper::appendWhere($where, 'usageType', $criType);
        
        
        $columns = array("usageTime","usageBy","ip","userAgent", "usageType", "description");
        
        $output = MongoTable::getOutput(
             "site_usage_history", $columns, array( "where" => $where, "totalWhere" => $totalWhere)
        );
    
        foreach ($output["aaData"] as &$row)
        {
            $row['usageTime']  = DateHelper::mongoDateToThai($row['usageTime']);
            $row['userAgent']  = self::formatUserAgent($row['userAgent']);
        }
    
        $output["message"] = self::errors();        
        return $output;
    }    
    
    private static function formatUserAgent($agent) {
        if (strlen($agent) > 30) {
            return "<span title='".$agent."'>".FormatHelper::truncate($agent,30)."</span>";
        }
        return $agent;
    }
    
    //**************************************************************************
    //
    // ADD DATA
    //
    //**************************************************************************
    
    
    public static function addData($request, $type, $description='') {
        $userId =  self::getLoginUserId();
        $agent = $request->server('HTTP_USER_AGENT');
        
        $data = array(
                "accountId" => self::getLoginAccountId(),
                "usageBy" => (!empty($userId))? $userId: "system",
                "usageTime" => MongoHelper::date(),
                "usageType" => $type,
                "description" => $description,
                "ip" => $_SERVER['REMOTE_ADDR'],
                "userAgent" => $agent
        );
    
        DB::table('site_usage_history')->insert($data);
    }
    
    /*
    public static function getUserAgent() {
        return "";
        
        if ($this->agent->is_browser()) {
            $agent = $this->agent->browser().' '.$this->agent->version();
        }
        else if ($this->agent->is_robot()) {
            $agent = $this->agent->robot();
        }
        else if ($this->agent->is_mobile()) {
            $agent = $this->agent->mobile();
        }
        else {
            $agent = "Unidentified (".$this->agent->agent_string().")";
        }
    
        return $agent;
    }
    */
    
}



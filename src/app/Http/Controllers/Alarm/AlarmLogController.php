<?php
namespace App\Http\Controllers\Alarm;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App;
use DataHelper;
use App\Http\Libraries\DropdownMgr;
use Log;
use App\Http\Models\Rdb;
use App\Http\Models\Alarm\AlarmLog;
use App\Http\Libraries\DateHelper;
use App\Http\Models\Core\MongoTable;

class AlarmLogController extends MyBaseController
{

    public $criteriaPrefix = "alarmLog";
    public $criteriaNames = array( "date", "toDate", "ackBy" );
    
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function index(Request $request)
    {

        if (!isset($_GET["keep"])) {
            $this->clearCache($request);
        }

        $this->data['message'] = $request->session()->has("message")? $request->session()->get("message"):"";
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();  
        $this->data['yesNoOpt'] = DropdownMgr::getYesNoArray();  
         
        $this->data['defaultDate'] = "";// DateHelper::todayThai();
        
       
        
        $this->setCriteriaDatas($request);

        if (empty($this->data['fieldDatas']['date'])) {
            $this->data['fieldDatas']['date'] = $this->data['defaultDate'];
        }

        
        return $this->openView('alarmLog.listAlarmLog', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }

    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }

    function getDataTable(Request $request) {
        $output = AlarmLog::getDatatable($request, $this->getCriteriaDatas($request));

        //DataHelper::debug($output);

        return response()->json($output);
    }
    
    function getDataTableForDashboard(Request $request) {
        $criDatas = array();
        $criDatas["ackBy"] = Rdb::$NO;
        
        if ($this->isSysadminAccount()) {
           $output = MongoTable::getEmptyOutput();
        }
        else {
           $output = AlarmLog::getDatatable($request, $criDatas);
        }
        
        return response()->json($output);
    }    
    
    

    //=======================================================
 
    function updateAckBy(Request $request) {
        $mongoId = $request->input("mongoId");
        $output = array();
        $userId = $this->getLoginUserId();
        
        if ( AlarmLog::updateAckBy($mongoId, $userId) ) {
            $output["ackBy"] = $userId;
        }
        else {
            $output["error"] = "ไม่สามารถอัปเดตข้อมูล";
        }

        return response()->json($output);
    }
 
    
    
}
<?php
namespace App\Http\Controllers\ImportLog;

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
use App\Http\Models\ImportLog\ImportLog;
use App\Http\Libraries\DateHelper;

class ImportLogController extends MyBaseController
{

    public $criteriaPrefix = "import_log";
    public $criteriaNames = array( "date", "to_date", "import_type");
    
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

        $this->data['default_date'] = "";// DateHelper::todayThai();
        
        $this->setCriteriaDatas($request);

        if (empty($this->data['fieldDatas']['date'])) {
            $this->data['fieldDatas']['date'] = $this->data['default_date'];
        }

        return $this->openView('importLog.listImportLog', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }

    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }

    function getDataTable(Request $request) {
        $output = ImportLog::getDatatable($request, $this->getCriteriaDatas($request));

        //DataHelper::debug($output);

        return response()->json($output);
    }

    //=======================================================
 

 
    
    
}
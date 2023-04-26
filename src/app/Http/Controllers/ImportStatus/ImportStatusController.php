<?php
namespace App\Http\Controllers\ImportStatus;

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
use App\Http\Models\ImportStatus\ImportStatus;

class ImportStatusController extends MyBaseController
{

    public $criteriaPrefix = "import_status";
    public $criteriaNames = array( "date", "to_date","name","active");
    
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

        $this->setCriteriaDatas($request);

        return $this->openView('importStatus.listImportStatus', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }

    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }

    function getDataTable(Request $request) {
        $output = ImportStatus::getDatatable($request, $this->getCriteriaDatas($request));

        //DataHelper::debug($output);

        return response()->json($output);
    }

    //=======================================================
 

      
    
    
}
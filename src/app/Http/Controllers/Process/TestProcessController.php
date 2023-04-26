<?php
namespace App\Http\Controllers\Process;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DropdownMgr;
use Log;
use App\Http\Models\Rdb;
use App\Http\Libraries\MongoHelper;
use App\Http\Libraries\FormatHelper;
use App\Http\Libraries\DateHelper;
use App\Http\Libraries\ExportPdf\DomPdfHelper;

class TestProcessController extends MyBaseController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function addImage(Request $request) {
        
        return $this->openView('testProcess.addImage', array());
    }
    
    public function testPdf1(Request $request) {
        
        return DomPdfHelper::generate("exportPdf/myTestPdf", array());
    }
    
    public function testPdf2(Request $request) {
        
        return $this->openView("exportPdf/myTestPdf", array());
    }    

}







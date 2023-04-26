<?php
namespace App\Http\Controllers\FileUpload;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\FileUpload\FileUpload;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;

class FileUploadPageController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "fileUploadPage";
    public $criteriaNames = array( "name","active","date","toDate");
    
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

        return $this->openView('fileUploadPage.listFileUploadPage', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = FileUpload::getDataTable($request, $this->getCriteriaDatas($request));
        
        //DataHelper::debug($output);
        
        return response()->json($output);
    }
    

    //=======================================================
 
    function getFieldLabels() {
        return [ 'work_site_id' => 'รหัส','name' => 'ชื่อ', 'status' => 'สถานะ' ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');
        $this->data['work_site_id'] = $request->input('work_site_id');
        $this->data['name'] = $request->input('name');     
        $this->data['title'] = $request->input('title');     
        $this->data['surname'] = $request->input('surname');     
        $this->data['length'] = $request->input('length');         
        
        $this->data['active'] = $request->input('active');

        $this->data['activeOpt'] = DropdownMgr::getActiveArray();           
        $this->data['valueDatas'] = "[]";
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? FileUpload::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($result)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {
            $this->data['mongoId'] = DataHelper::getMyProp($result, "mongoId", "");
            $this->data['work_site_id'] = DataHelper::getMyProp($result, "work_site_id", "");
            $this->data['name'] = DataHelper::getMyProp($result, "name", "");
            $this->data['active'] =  DataHelper::getMyProp($result, "active", "");
            $this->data['title'] = DataHelper::getMyProp($result, "title", "");
            $this->data['surname'] = DataHelper::getMyProp($result, "surname", "");

        
        }
    }

    

    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('fileUploadPage.addFileUploadPage', $this->data);
    
    }
    
 
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $object = FileUpload::getData($paramId);
        
        if (!$object) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            if (FileUpload::deleteData($paramId, $object)) {

                $request->session()->flash('message', "ลบข้อมูล '".$object['name']."' แล้ว");
            }
            else {
                $request->session()->flash('message', FileUpload::errors());
            }
        }
         
        return redirect("fileUploadPage/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}






 
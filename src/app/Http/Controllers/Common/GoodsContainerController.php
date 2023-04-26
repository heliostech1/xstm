<?php
namespace App\Http\Controllers\Common;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Models\Common\GoodsContainer;

class GoodsContainerController extends MyBaseController
{
    public $criteriaPrefix = "goodsContainer";
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

        return $this->openView('commonRdb.listCommonRdb', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = GoodsContainer::getDataTable($request, $this->getCriteriaDatas($request));
        
        return response()->json($output);
    }
    

    //=======================================================
 
    function getFieldLabels() {
        return [ 'name' => 'ชื่อ', 'status' => 'สถานะ' ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');
        $this->data['name'] = $request->input('name');     
        $this->data['active'] = $request->input('active');
       

        $this->data['activeOpt'] = DropdownMgr::getActiveArray();        
        $this->data['valueDatas'] = "[]";
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? GoodsContainer::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($result)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {
            $this->data['mongoId'] = DataHelper::getMyProp($result, "mongoId", "");
            $this->data['name'] = DataHelper::getMyProp($result, "name", "");
            $this->data['active'] =  DataHelper::getMyProp($result, "active", "");

        
        }
    }
    
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('commonRdb.addCommonRdb', $this->data);
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('commonRdb.addCommonRdb', $this->data);
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('commonRdb.addCommonRdb', $this->data);
    
    }
    
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
              //  'goodsContainerId' => 'required',
                'name' => 'required|max:1000',
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {
            $inputDatas = array(
                'name' => $request->input('name'),
            );   

            if ( GoodsContainer::addData($inputDatas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลใหม่แล้ว");
                return redirect("goodsContainer/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, GoodsContainer::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';
        
        return $this->openView('commonRdb.addCommonRdb', $this->data);
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
                'name' => 'required|max:1000',
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {            
            $mongoId = $request->input('mongoId');
            $inputDatas = array(
                'name' => $request->input('name'),
                'active' => $request->input('active'),      
            );
                       
            if ( GoodsContainer::editData($mongoId, $inputDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("goodsContainer/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, GoodsContainer::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'edit';
        return $this->openView('commonRdb.addCommonRdb', $this->data);
    }    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $object = GoodsContainer::getData($paramId);
        
        if (!$object) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            if (GoodsContainer::deleteData($paramId)) {
                $request->session()->flash('message', "ลบข้อมูล '".$object['name']."' แล้ว");
            }
            else {
                $request->session()->flash('message', GoodsContainer::errors());
            }
        }
         
        return redirect("goodsContainer/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}







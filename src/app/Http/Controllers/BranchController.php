<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DropdownMgr;
use Log;
use App\Http\Models\Branch;
use App\Http\Models\Rdb;


class BranchController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "branch";
    public $criteriaNames = array( "branch_code","name","active");
    
    public function __construct()
    {
        parent::__construct(true, false);
    }
    
    
    public function index(Request $request)
    {
        if (!isset($_GET["keep"])) {
            $this->clearCache($request);
        }

        $this->data['message'] = $request->session()->has("message")? $request->session()->get("message"):"";
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();
        
        $this->setCriteriaDatas($request);

        return $this->openView('branch.listBranch', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = Branch::getDataTable($request, $this->getCriteriaDatas($request));
        
        //DataHelper::debug($output);
        
        return response()->json($output);
    }
    

    //=======================================================
 
    function getFieldLabels() {
        return [ 'branch_code' => 'รหัส','name' => 'ชื่อ', 'status' => 'สถานะ' ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');
        $this->data['branch_code'] = $request->input('branch_code');
        $this->data['name'] = $request->input('name');     
        $this->data['active'] = $request->input('active');
        $this->data['invoice_address_th'] = $request->input('invoice_address_th');
        $this->data['invoice_address_en'] = $request->input('invoice_address_en');
                
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();           
        $this->data['valueDatas'] = "[]";
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $branch = (!empty($paramId))? Branch::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($branch)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {
            $this->data['mongoId'] = isset($branch['mongoId'])? $branch['mongoId']:"";
            $this->data['branch_code'] = isset($branch['branch_code'])? $branch['branch_code']:"";
            $this->data['name'] = isset($branch['name'])? $branch['name']:"";
            $this->data['active'] =  isset($branch['active'])? $branch['active']:""; 
            $this->data['invoice_address_th'] =  isset($branch['invoice_address_th'])? $branch['invoice_address_th']:"";        
            $this->data['invoice_address_en'] =  isset($branch['invoice_address_en'])? $branch['invoice_address_en']:"";      
                        
            
        }
    }
    
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('branch.addBranch', $this->data);
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('branch.addBranch', $this->data);
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('branch.addBranch', $this->data);
    
    }
    
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
                'branch_code' => 'required',
                'name' => 'required|max:1000',
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {
            $inputDatas = array(
                'branch_code' => $request->input('branch_code'),
                'name' => $request->input('name'),
                'invoice_address_th' => $request->input('invoice_address_th'),  
                'invoice_address_en' => $request->input('invoice_address_en'),                   
            );   
            
            if ( Branch::addData($inputDatas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลใหม่แล้ว");
                return redirect("branch/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, Branch::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';
        
        return $this->openView('branch.addBranch', $this->data);
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
                'branch_code' => 'required',
                'name' => 'required|max:1000',
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {            
            $mongoId = $request->input('mongoId');
            $inputDatas = array(
                'branch_code' => $request->input('branch_code'),
                'name' => $request->input('name'),
                'active' => $request->input('active'),
                'invoice_address_th' => $request->input('invoice_address_th'),  
                'invoice_address_en' => $request->input('invoice_address_en'),                   
            );
                       
            if ( Branch::editData($mongoId, $inputDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("branch/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, Branch::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'edit';
        return $this->openView('branch.addBranch', $this->data);
    }    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $object = Branch::getData($paramId);
        
        if (empty($object)) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            if (Branch::deleteData($paramId)) {
                $request->session()->flash('message', "ลบข้อมูล '".$object['name']."' แล้ว");
            }
            else {
                $request->session()->flash('message', Branch::errors());
            }
        }
         
        return redirect("branch/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}







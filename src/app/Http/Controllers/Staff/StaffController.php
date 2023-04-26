<?php
namespace App\Http\Controllers\Staff;

use App;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\Staff\Staff;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\BigPage\StaffHelper;

class StaffController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "staff";
    public $criteriaNames = array( "staffCode","staffName", "workCompany",
        "staffType", "licensePlate","vehicleType", "workStatus"
        
     );

    
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
        $this->data['defaultWorkStatus'] = Rdb::$YES;
        
        $this->setCriteriaDatas($request);
        $this->setCriteriaDefaultData('workStatus',  $this->data['defaultWorkStatus']);
        
        $this->data['workCompanyOpt'] = DropdownMgr::getWorkCompanyArray();         
        $this->data['staffTypeOpt'] = DropdownMgr::getStaffTypeArray();         
        $this->data['licenseTypeOpt'] = DropdownMgr::getLicenseTypeArray(); 
        $this->data['workStatusOpt'] = DropdownMgr::getWorkStatusArray(); 
        
        $this->data['licensePlateList'] = DropdownMgr::getLicensePlateList();  
        
        return $this->openView('staff.listStaff', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = Staff::getDataTable($request, $this->getCriteriaDatas($request));

        return response()->json($output);
    }
    
    function getPopupDataTable(Request $request) {
        $criDatas = array();
        $criDatas['staffName'] = $request->input('criStaffName');
        $criDatas['workCompany'] = $request->input('criWorkCompany');
        $criDatas['staffType'] = $request->input('criStaffType');
        $criDatas['workStatus'] = Rdb::$YES;
        
        $output = Staff::getDataTable($request, $criDatas );

        return response()->json($output);
    }
    

    
    //=======================================================
 
    function getFieldLabels() {
        return [ 'staffCode' => 'รหัส','staffName' => 'ชื่อ', 'licensePlate' => 'ทะเบียนรถ', 'status' => 'สถานะ' ];
    }
    
    function _collectPostData($request, $mode="") {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['mongoId'] = $request->input('mongoId');        
        $this->data['staffCode'] = $request->input('staffCode');     
        $this->data['staffName'] = $request->input('staffName');           
        $this->data['active'] = $request->input('active');
      
        $this->data = array_merge( 
                $this->data ,
                StaffHelper::collectRequest($request, "partBase_", StaffHelper::getBaseFields(), StaffHelper::getBaseArrayFields()) ,                
                StaffHelper::collectRequest($request, "partLicense_", StaffHelper::getLicenseFields(), StaffHelper::getLicenseArrayFields()) ,
                StaffHelper::collectRequest($request, "partWork_",  StaffHelper::getWorkFields(), StaffHelper::getWorkArrayFields()),
                StaffHelper::collectRequest($request, "partAbsent_", StaffHelper::getAbsentFields(), StaffHelper::getAbsentArrayFields())
        );
        
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();  
        $this->data['yesNoOpt'] = DropdownMgr::getYesNoArray(); 

        $this->data['workCompanyOpt'] = DropdownMgr::getWorkCompanyArray();         
        $this->data['staffTypeOpt'] = DropdownMgr::getStaffTypeArray();         
        $this->data['licenseTypeOpt'] = DropdownMgr::getLicenseTypeArray(); 
        $this->data['workStatusOpt'] = DropdownMgr::getWorkStatusArray();      
        
        //$this->data['vehicleOpt'] = DropdownMgr::getVehicleArray(); 
                        
        $this->data['valueDatas'] = "[]";
        $this->data['fileDatas'] = "[]";         
    }
    
    function _getDataForViewEdit($request, $mode="") {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? Staff::getData($paramId): null;
        $this->_collectPostData($request, $mode);
    
        if (empty($result)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {
            $this->data['mongoId'] = getMyProp($result, "mongoId", "");              
            $this->data['active'] =  getMyProp($result, "active", "");
            $this->data['staffCode'] =  getMyProp($result, "staffCode", "");
            $this->data['staffName'] =  getMyProp($result, "staffName", "");            
            $this->data['fileDatas'] = isset($result['fileDatas'])? json_encode($result['fileDatas']): "[]";   
            
            $this->data = array_merge( 
                $this->data ,
                StaffHelper::getResultForGet($result, "partBase_", StaffHelper::getBaseFields(), StaffHelper::getBaseArrayFields()) ,                
                StaffHelper::getResultForGet($result, "partLicense_", StaffHelper::getLicenseFields(), StaffHelper::getLicenseArrayFields()) ,
                StaffHelper::getResultForGet($result, "partWork_",  StaffHelper::getWorkFields(), StaffHelper::getWorkArrayFields()),
                StaffHelper::getResultForGet($result, "partAbsent_", StaffHelper::getAbsentFields(), StaffHelper::getAbsentArrayFields())              
            );        

        }
    }
    
              
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('staff.addStaff', $this->data, array("fileUpload"=>true));
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('staff.addStaff', $this->data, array("fileUpload"=>true));
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request, "view");
        $this->data['pageMode'] = 'view';
    
        return $this->openView('staff.addStaff', $this->data, array("fileUpload"=>true));    
    }

    
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
                'staffName' => 'required',
        ],[], $this->getFieldLabels());
        
       // $fileDatas = json_decode($request->input('fileDatas'));
        
        if (!$validator->fails())
        {                      
            $inputDatas = array(
                'staffCode' => $request->input('staffCode'),
                'staffName' => $request->input('staffName'),      
            );   

            $inputDatas = $this->collectPartDataForSave($request, $inputDatas);
            
            if ( Staff::addData($inputDatas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลใหม่แล้ว");
                return redirect("staff/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, Staff::getErrors());
        $this->_collectPostData($request);
       // $this->data['fileDatas'] = json_encode($fileDatas);           
        $this->data['pageMode'] = 'add';
        
        return $this->openView('staff.addStaff', $this->data, array("fileUpload"=>true));
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'mongoId' => 'required',
                'staffName' => 'required',
        ],[], $this->getFieldLabels());
        
        if (!$validator->fails())
        {            
            $mongoId = $request->input('mongoId');
            $inputDatas = array(
                'staffCode' => $request->input('staffCode'),
                'staffName' => $request->input('staffName'),  
                'active' => $request->input('active'),           
            );
                    
            $inputDatas = $this->collectPartDataForSave($request, $inputDatas);

            if ( Staff::editData($mongoId, $inputDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("staff/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, Staff::getErrors());
        $this->_collectPostData($request);        
        $this->data['pageMode'] = 'edit';
        return $this->openView('staff.addStaff', $this->data, array("fileUpload"=>true));
    }    
    
    
    private function collectPartDataForSave($request, $inputDatas) {
        if ($this->hasPermission('staff/edit')) {
            $inputDatas['partBase_'] = StaffHelper::getRequestForSave($request,"partBase_",  StaffHelper::getBaseFields(), StaffHelper::getBaseArrayFields() );
        }
        
        if ($this->hasPermission('staff/editLicense')) {
            $inputDatas['partLicense_'] = StaffHelper::getRequestForSave($request,"partLicense_", StaffHelper::getLicenseFields(), StaffHelper::getLicenseArrayFields() );
        }
        
        if ($this->hasPermission('staff/editWork')) {
            $inputDatas['partWork_'] = StaffHelper::getRequestForSave($request,"partWork_", StaffHelper::getWorkFields(), StaffHelper::getWorkArrayFields() );  
            
            if (empty($inputDatas['partWork_']['workStatus'])) {
                $inputDatas['partWork_']['workStatus'] = Rdb::$YES;
            }
            
        }
        
        if ($this->hasPermission('staff/editAbsent')) {
            $inputDatas['partAbsent_'] = StaffHelper::getRequestForSave($request,"partAbsent_", StaffHelper::getAbsentFields(), StaffHelper::getAbsentArrayFields() ); 
        }
        
        return $inputDatas;
    }
    
    
    private function hasPermission($page = '') {
        return App::make("AuthMgr")->hasPagePermission($page);
    }
    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $object = Staff::getData($paramId);
        
        if (!$object) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            if (Staff::deleteData($paramId)) {
                $request->session()->flash('message', "ลบข้อมูล '".$object['staffName']."' แล้ว");
            }
            else {
                $request->session()->flash('message', Staff::errors());
            }
        }
         
        return redirect("staff/index?keep=1");
        
    }
    
    //====================================================================================
    //
    // VALIDATE
    //
    //====================================================================================
 
}







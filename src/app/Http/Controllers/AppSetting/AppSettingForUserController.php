<?php
namespace App\Http\Controllers\AppSetting;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Controllers\MyBaseController;
use App\Http\Libraries\DropdownMgr;
use App\Http\Models\Common\Driver;
use App\Http\Models\Rdb;
use App\Http\Libraries\DataHelper;
use App\Http\Models\AppSetting\AppSettingForUser;


class AppSettingForUserController extends MyBaseController
{

    public $criteriaPrefix = "appSettingForUser";
    public $criteriaNames = array( "date", "toDate","name","active");
    
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
        $this->data['tableDatas'] = json_encode(AppSettingForUser::getDataTableArray());
        
        $this->setCriteriaDatas($request);

        return $this->openView('appSettingForUser.listAppSettingForUser', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    

    //=======================================================
 
    function getFieldLabels() {
        return [ 'settingId' => 'รหัส','name' => 'ชื่อ', 'status' => 'สถานะ' ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['settingId'] = $request->input('settingId');
        $this->data['name'] = $request->input('name');     
        $this->data['value'] = $request->input('value');     
        $this->data['unit'] = $request->input('unit');     
        $this->data['category'] = $request->input('category'); 
        
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();           
        $this->data['valueDatas'] = "[]";
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? AppSettingForUser::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($result)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$paramId;
        }
        else {
            $this->data['mongoId'] = DataHelper::getMyProp($result, "mongoId", "");
            $this->data['settingId'] = DataHelper::getMyProp($result, "settingId", "");
            $this->data['name'] = DataHelper::getMyProp($result, "name", "");
            $this->data['value'] =  DataHelper::getMyProp($result, "value", "");
            $this->data['unit'] = DataHelper::getMyProp($result, "unit", "");
            $this->data['category'] = DataHelper::getMyProp($result, "category", "");            

        }
    }

    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('appSettingForUser.addAppSettingForUser', $this->data);
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('appSettingForUser.addAppSettingForUser', $this->data);
    
    }


    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
                'settingId' => 'required',

        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {            
            $settingId = $request->input('settingId');
            $inputDatas = array(
                'settingId' => $request->input('settingId'),
                'value' => $request->input('value'),
                'unit' => $request->input('unit'),                            
            );
                       
            if ( AppSettingForUser::editData($settingId, $inputDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("appSettingForUser/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, AppSettingForUser::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'edit';
        return $this->openView('appSettingForUser.addAppSettingForUser', $this->data);
    }    
    

}







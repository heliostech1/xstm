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
use App\Http\Models\Rdb;
use App\Http\Models\Alarm\AlarmSetting;

class AlarmSettingController extends MyBaseController
{

    public $criteriaPrefix = "alarmSetting";
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
        $this->data['tableDatas'] = json_encode(AlarmSetting::getDatatable());
  
        $this->setCriteriaDatas($request);

        return $this->openView('alarmSetting.listAlarmSetting', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }

    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }

    function getDataTable(Request $request) {
        //$output = AlarmSetting::getDatatable($request, $this->getCriteriaDatas($request));

        //DataHelper::debug($output);
        $output = array();
        return response()->json($output);
    }

    //=======================================================
 
    function getFieldLabels() {
        return [ 'settingId' => 'ID','name' => 'Name'];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['settingId'] = $request->input('settingId');
        $this->data['name'] = $request->input('name');
        $this->data['enable'] = $request->input('enable');     
        $this->data['alarmTimeForCheckDate'] = $request->input('alarmTimeForCheckDate');
        $this->data['alarmTimeForCheckOdo'] = $request->input('alarmTimeForCheckOdo');     
    
        $this->data['yesNoOpt'] = DropdownMgr::getYesNoArray();    
        $this->data['requiredOpt'] = DropdownMgr::getYesNoArray(false);      
        $this->data['valueDatas'] = "[]";
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        //DataHelper::debug($paramId);
        
        $result = (!empty($paramId))? AlarmSetting::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($result)) {
            $this->data['message'] = "Not found:".$paramId;
        }
        else {
            $this->data['settingId'] = isset($result['settingId'])? $result['settingId']:"";
            $this->data['name'] = isset($result['name'])? $result['name']:"";
            $this->data['enable'] = isset($result['enable'])? $result['enable']:"";            
            $this->data['alarmTimeForCheckDate'] =  isset($result['alarmTimeForCheckDate'])? $result['alarmTimeForCheckDate']:""; 
            $this->data['alarmTimeForCheckOdo'] = isset($result['alarmTimeForCheckOdo'])? $result['alarmTimeForCheckOdo']:"";         

        }
    }
    
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('alarmSetting.addAlarmSetting', $this->data);
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('alarmSetting.addAlarmSetting', $this->data);
    
    }
    
    
    function editSubmit(Request $request) {
    
        $validator = $this->genValidator($request, [
               'settingId' => 'required',
         
        ],[], $this->getFieldLabels());
        
        
        if (!$validator->fails())
        {            
        
            $keyId = $request->input('settingId');
            
            $inputDatas = array(
                'settingId' => $request->input('settingId'),
                'name' => $request->input('name'),
                'enable' => $request->input('enable'),
                'alarmTimeForCheckDate' => $request->input('alarmTimeForCheckDate'),               
                'alarmTimeForCheckOdo' => $request->input('alarmTimeForCheckOdo'),
      
            );
                       
            if ( AlarmSetting::editData($keyId, $inputDatas)) {
                $request->session()->flash('message', Rdb::getUpdateSuccess());
                return redirect("alarmSetting/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, AlarmSetting::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'edit';
        return $this->openView('alarmSetting.addAlarmSetting', $this->data);
    }    
    
    
}
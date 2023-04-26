<?php
namespace App\Http\Controllers\Account;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App\Http\Models\Account;
use App\Http\Controllers\MyBaseController;
use App;
use App\Http\Libraries\DataHelper;
use App\Http\Libraries\DropdownMgr;
use Log;
use App\Http\Models\Rdb;

class AccountController extends MyBaseController
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    public $criteriaPrefix = "account";
    public $criteriaNames = array( "accountId");
    
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
        $this->data['isSysadminAccount'] = $this->isSysadminAccount();
                
        $this->setCriteriaDatas($request);

        return $this->openView('account.listAccount', $this->data);
    }

    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }
    
    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }    
    
    function getDataTable(Request $request) {
        $output = Account::getDataTable($request, $this->getCriteriaDatas($request));
        
        //DataHelper::debug($output);
        
        return response()->json($output);
    }
    
    //=======================================================
    
    function getFieldLabels() {
        return [ 'accountId' => 'บัญชี','description' => 'รายละเอียด', 'app_plan_id' => 'แผนการใข้งาน',
             'contactName' => 'ชื่อที่ติดต่อได้','contactPhone' => 'โทรศัพท์ที่ติดต่อได้','contactEmail' => 'อีเมล์ที่ติดต่อได้',
             'active' => 'สถานะ', 'userId' => 'รหัสผู้ใช้เริ่มต้น',  'password' => 'รหัสผ่าน', 'password_confirm' => 'ยืนยันรหัสผ่าน',
        ];
    }
    
    function _collectPostData($request) {
    
        $this->data['fieldLabels'] = $this->getFieldLabels();
        
        $this->data['accountId'] = $request->input('accountId');
        $this->data['description'] = $request->input('description');
        $this->data['contactName'] = $request->input('contactName');
        $this->data['contactPhone'] = $request->input('contactPhone');
        $this->data['contactEmail'] = $request->input('contactEmail');
        $this->data['active'] = $request->input('active');
    
        $this->data['change_password'] = (isset($_POST['change_password']))? "checked": "";
        $this->data['userId'] = $request->input('userId');
        $this->data['password'] = $request->input('password');
        $this->data['password_confirm'] = $request->input('password_confirm');        
        $this->data['app_plan_id'] = $request->input('app_plan_id'); 
        
        $this->data['company_name'] = $request->input('company_name');
        $this->data['tax_id'] = $request->input('tax_id');
        $this->data['company_address'] = $request->input('company_address');
        $this->data['invoice_address_th'] = $request->input('invoice_address_th');
        $this->data['invoice_address_en'] = $request->input('invoice_address_en');
        
        $this->data['default_sale_date'] = $request->input('default_sale_date');
        $this->data['config_quote_doc_add_text'] = $request->input('config_quote_doc_add_text');
        
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();        
        $this->data['app_plan_opt'] = DropdownMgr::getAppPlanArray();   
    }
    
    function _getDataForViewEdit($request) {
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        $account = (!empty($paramId))? Account::getData($paramId): null;
        $this->_collectPostData($request);
    
        if (empty($account)) {
            $this->data['message'] = "ไม่พบรหัสข้อมูล:".$param_id;
        }
        else {
            $this->data['accountId'] =  isset($account['accountId'])? $account['accountId']:"";
            $this->data['description'] =  isset($account['description'])? $account['description']:"";
            $this->data['contactName'] =  isset($account['contactName'])? $account['contactName']:"";
            $this->data['contactPhone'] =  isset($account['contactPhone'])? $account['contactPhone']:"";
            $this->data['contactEmail'] =  isset($account['contactEmail'])? $account['contactEmail']:"";
            $this->data['active'] =  isset($account['active'])? $account['active']:"";
            $this->data['userId'] = isset($account['default_userId'])? $account['default_userId']:"";     
            $this->data['app_plan_id'] =  isset($account['app_plan_id'])? $account['app_plan_id']:"";         
            
            $this->data['company_name'] =  isset($account['company_name'])? $account['company_name']:"";
            $this->data['tax_id'] = isset($account['tax_id'])? $account['tax_id']:"";     
            $this->data['company_address'] =  isset($account['company_address'])? $account['company_address']:"";              
            $this->data['invoice_address_th'] =  isset($account['invoice_address_th'])? $account['invoice_address_th']:"";        
            $this->data['invoice_address_en'] =  isset($account['invoice_address_en'])? $account['invoice_address_en']:"";      
            
            $this->data['default_sale_date'] =  isset($account['default_sale_date'])? $account['default_sale_date']:"";          
            $this->data['config_quote_doc_add_text'] =  isset($account['config_quote_doc_add_text'])? $account['config_quote_doc_add_text']:"";               
        }
    }
    
    function add(Request $request)
    {
        $this->cache($request);
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';

        return $this->openView('account.addAccount', $this->data);
    
    }
    
    function edit(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('account.addAccount', $this->data);
    
    }    
    
    function view(Request $request)
    {
        $this->cache($request);
        $this->_getDataForViewEdit($request);
        $this->data['pageMode'] = 'view';
    
        return $this->openView('account.addAccount', $this->data);
    
    }
    
    function addSubmit(Request $request) {

        $validator = $this->genValidator($request, [
                'accountId' => 'required|max:50',
                'password' => 'max:50|min:8',
                'password_confirm' => 'same:password',      
      
         
        ],[], $this->getFieldLabels());
        
        if (!$validator->fails())
        {
            $accountId = strtolower($request->input('accountId'));
            $userId = strtolower($request->input('userId'));
            
            $accountDatas = array(
                'accountId' => $accountId,
                'description' => $request->input('description'),
                'contactName' => $request->input('contactName'),
                'contactPhone' => $request->input('contactPhone'),
                'contactEmail' => $request->input('contactEmail'),
                'default_userId' => $userId,
                'app_plan_id' =>   Rdb::$APP_PLAN_ADVANCE, //   $request->input('app_plan_id'),
                
                'company_name' => $request->input('company_name'),
                'tax_id' => $request->input('tax_id'),
                'company_address' => $request->input('company_address'),                
                'invoice_address_th' => $request->input('invoice_address_th'),  
                'invoice_address_en' => $request->input('invoice_address_en'),            
                
                'default_sale_date' => $request->input('default_sale_date'),          
                'config_quote_doc_add_text' => $request->input('config_quote_doc_add_text'),                  
            );   
    
            $userDatas = array(
                'userId' => $userId,
                'password' => $request->input('password'),
            );
            
            if ( Account::addData($accountId, $accountDatas, $userDatas)) {     
                $request->session()->flash('message', "เพิ่มข้อมูลใหม่แล้ว");
                return redirect("account/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, Account::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'add';
        
        //DataHelper::debug( $this->data, "ERROR:");
        return $this->openView('account.addAccount', $this->data);
    }
    
    
    function editSubmit(Request $request) {
    
        $accountId = strtolower($request->input('accountId'));        
        $userId = strtolower($request->input('userId'));

        $validateRules =  [
            'accountId' => 'required|max:50',
            //'account_group_id' => '',
        ];
        
        if (isset($_POST['change_password'])) {
            $validateRules['userId'] = 'required';
            $validateRules['password'] =  'required|max:50|min:8';
            $validateRules['password_confirm'] =  'required|same:password';
        }        
        
        $validator = $this->genValidator($request, $validateRules,[], $this->getFieldLabels());
    
        if (!$validator->fails())
        {
            $accountDatas = array(
                'active' => $request->input('active'),
                'description' => $request->input('description'),
                'contactName' => $request->input('contactName'),
                'contactPhone' => $request->input('contactPhone'),
                'contactEmail' => $request->input('contactEmail'), 
                'app_plan_id' =>   Rdb::$APP_PLAN_ADVANCE, //   $request->input('app_plan_id'),
                                
                'company_name' => $request->input('company_name'),
                'tax_id' => $request->input('tax_id'),
                'company_address' => $request->input('company_address'),            
                'invoice_address_th' => $request->input('invoice_address_th'),  
                'invoice_address_en' => $request->input('invoice_address_en'),       
                
                'default_sale_date' => $request->input('default_sale_date'),          
                'config_quote_doc_add_text' => $request->input('config_quote_doc_add_text'),                  
            );
    
            $userDatas = null;
            if (isset($_POST['change_password'])) {
                $accountDatas['default_userId'] = $userId;
                $userDatas = array(
                    'userId' => $userId,
                    'password' => $request->input('password'),
                );
            }
            
            if ( Account::editData($accountId, $accountDatas, $userDatas)) {
                $request->session()->flash('message', "แก้ไขข้อมูลแล้ว");
                return redirect("account/index?keep=1");
            }
        }
    
        $this->data['message'] = $this->getResponseMessage($request, $validator, Account::getErrors());
        $this->_collectPostData($request);
        $this->data['pageMode'] = 'edit';
    
        return $this->openView('account.addAccount', $this->data);
    }    
    
    function delete(Request $request)
    {
        $this->cache($request);
        
        $paramId = $request->input($this->criteriaPrefix.'_tableSelectedId');
        
        if (!Account::isExist($paramId)) {
            $request->session()->flash('message', "ไม่พบรหัสข้อมูล '".$paramId."'");
        }
        else {
            if (Account::deleteData($paramId)) {
                $request->session()->flash('message', "ลบข้อมูล '".$paramId."' แล้ว");
            }
            else {
                $request->session()->flash('message', Account::errors());
            }
        }
         
        return redirect("account/index?keep=1");
        
    }
    
  
}







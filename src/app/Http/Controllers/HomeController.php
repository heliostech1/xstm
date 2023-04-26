<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App;
use Illuminate\Support\Facades\Log;
use App\Http\Libraries\MenuMgr;
use App\Http\Libraries\DropdownMgr;

class HomeController extends MyBaseController
{
    
    public $criteriaPrefix = "home";
    public $criteriaNames = array( "date", "toDate");
    
    
    public function __construct()
    {
        parent::__construct(true, false);
        //$this->middleware('auth');
    }
    
    public function index(Request $request)
    {
        if (!isset($_GET["keep"])) {
            $this->clearCache($request);
        }

        $this->data['message'] = $request->session()->has("message")? $request->session()->get("message"):"";
        $this->data['activeOpt'] = DropdownMgr::getActiveArray();  
        $this->data['yesNoOpt'] = DropdownMgr::getYesNoArray();  
        //$this->data['menuList'] = App::make("MenuMgr")->getMenu(false);
        
        $this->setCriteriaDatas($request);

        if (empty($this->getLoginBranchId()) && !$this->isSysadminAccount()) {
       //    $this->data['showBranchPopup'] = true;
        }        

        return $this->openView('home', $this->data);
    }
    
    function cache($request) {
        $this->cacheCriteriaDatas($request);
    }

    function clearCache($request) {
        $this->clearCacheCriteriaDatas($request);
    }
    
    
    
}

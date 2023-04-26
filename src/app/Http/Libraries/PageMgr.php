<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use App\Http\Models\User;
use App;
use URL;
use App\Http\Models\Rdb;
use App\Http\Models\Branch;
use App\Http\Models\UserGroup;
use App\Http\Libraries\SiteHelper;

class PageMgr extends MyBaseLib
{        
    public $app;
    public $request;
    public $session;
    
    public function __construct($app)
    {  
        //Log::debug("AuthMgr construct");
        $this->app = $app;
        $this->request = $this->app['request'];
        $this->session = $this->app['request']->session();
    }
    
    public function view($pageName, $data=null, $options=null) {
        
        $data['appTitle'] = config('app.appTitle');
        $data['appCode'] = config('app.appCode');
        
        $data['pageBuildDate'] =  $this->getPageBuildDate(); // $this->formatBuildDate(config('app.buildDate'));
        $data['pageBuildRevision'] = $this->formatBuildRevision(config('app.buildRevision'));

        $data['isLoggedIn'] = $this->app["AuthMgr"]->isLoggedIn();
        $data['appPlanId'] = $this->app["AuthMgr"]->getLoginAppPlanId();
        $data['isStartupPlan'] = false; // ( $data['appPlanId'] == Rdb::$APP_PLAN_STARTUP)? true:false;
        $data['isBasicPlan'] = false; // ( $data['appPlanId'] == Rdb::$APP_PLAN_BASIC)? true:false;        
        $data['isAdvancePlan'] = true; // ( $data['appPlanId'] == Rdb::$APP_PLAN_ADVANCE)? true:false;
        
        $data['pageFile'] = $pageName;
        $data['pageName'] = $pageName;
        
        $data['pageMenuList'] =  "";
        $data['pageNavbarUser'] = "";        
        $data['pageNavbarBranch'] = "";
        
        if ($pageName != "login") {
            $data['pageMenuList'] =  $this->app["MenuMgr"]->getMenu(); 
            $data['pageNavbarUser'] = $this->getUserInfo();
            $data['pageNavbarBranch'] = $this->getNavbarBranchInfo();            
        }
        
        if ($this->app["AuthMgr"]->isLoggedIn()) {
            $data['pageNavbar'] = '<a href="'.URL::to('/logout').'">ออกจากระบบ</a>';
        }
        else {
            $data['pageNavbar'] = '<a href="'.URL::to('/login').'">เข้าสู่ระบบ</a>';
        }
        
        $headerColors = $this->getHeaderColors();
        $data['pageHeaderColorBase'] = $headerColors['base'];
        $data['pageHeaderColorPale'] = $headerColors['pale'];
        $data['pageHeaderColorDark'] = $headerColors['dark'];
        
        $data['pageOptLoadMap'] =  false;
        $data['pageOptHideMenu'] =  false;
        $data['pageOptAutoWidth'] =  false;
        $data['pageOptFileUpload'] = (  $this->getOptionsVal($options, 'fileUpload')  === true)? true: false;
        $data['pageOptChildPage'] =  ( $this->getOptionsVal($options, 'childPage')  === true)? true: false;
        $data['pageOptChart'] =  ( $this->getOptionsVal($options, 'chart')  === true)? true: false;
        
        if ($data['pageOptChildPage']) {
             $data['pageNavbar'] = '<a href="#" style="color:white;" onclick="window.close()">ปิด</a>';
        }
        
        $data['sitePageWidth'] =  $this->getSitePageWidth($data['appCode']."_sitePageWidth");
        
        $data['CONST_DATE_TODAY'] = DateHelper::nowThai();
        $data['CONST_COOKIE_DOMAIN'] = $this->getCookieDomain();
        $data['CONST_AP_STARTUP'] = Rdb::$APP_PLAN_STARTUP;
        
        $data = $this->setAdditionalData($data);
        //DataHelper::debug($data);
        
        return view($pageName, $data);
    }
    
    private  function getOptionsVal($options, $name) {
        if ($options && isset($options[$name])) {
            return $options[$name];
        }
        return null;
    }
    
    private function setAdditionalData($data) {
        $pageId =  $this->getCurrentPageId();
        //$userGroupId = $this->ci->auth_mgr->get_login_user_group();
        $pageId = (empty($pageId) || $pageId == "/")? "home": $pageId;
        $page = App::make("PageFactory")->getPage($pageId);
    
        //SET PAGE NAME
        $data['sitePageId'] = $pageId;
        $data['sitePageName'] = ($page)? $page->page_no." ".$page->name: "ไม่พบหน้าที่ระบุ";
        $data['sitePageMenu'] = ($page)? $page->menu: "ไม่พบหน้าที่ระบุ";
        $data['sitePageDesc'] = ($page)? $page->description.":": "ไม่พบหน้าที่ระบุ";
       
        $data['siteBranchId'] = $this->app["AuthMgr"]->getLoginBranchId();
        $data['siteBranchDatas'] = Branch::getDatasForChoosePopup();
        
        return $data;
    }
    
    private function getUserInfo() {
        $info = "ผู้ใช้ระบบ: ". $this->app["AuthMgr"]->getLoggedInUser();
        $info .= " ( บัญชี: ". $this->app["AuthMgr"]->getLoginAccountId();
        $info .= " , กลุ่มผู้ใช้: ". $this->app["AuthMgr"]->getLoginUserGroupName();
      //  $info .= ", แผน: ". $this->app["AuthMgr"]->getLoginAppPlanIdShort();        
        $info .= " )";
        return $info;
    }
    
    private function getNavbarBranchInfo() {
        return "";
        
        if ($this->app["AuthMgr"]->getLoginUserGroup() == Rdb::$USER_GROUP_SYSADMIN) {
            return "";
        }
        
        $data = $this->app["AuthMgr"]->getLoginBranchName();
        $data = (empty($data))? "-- กรุณาเลือก --": $data;
                                   
        return  "<a href='#' style='color:white;' onclick='siteBranchPopup_openPopup()' >สาขา: $data</a>";
    }
    
    private function getSitePageWidth($cookieName) {
        if (isset($_COOKIE[$cookieName])) {
             return str_replace("%", "", $_COOKIE[$cookieName]);
        }        
        return "";
    }
    
    private function getCookieDomain() {
        $domain = '';
        $host = parse_url('http://'.$_SERVER['SERVER_NAME'], PHP_URL_HOST);
        if (preg_match('/([a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $host, $m)) {
            $domain = $m[1];
        }
        //echo "$host $domain AAA";
        return $domain;
    }
    
    private function getHeaderColors() {
        $userGroupId = $this->app["AuthMgr"]->getLoginUserGroup();
        $color = UserGroup::getUserGroupColor($userGroupId);
        
        $base = "#0f487b"; // default; "#4b325a"
        $pale = "#115591";
        $dark = ""; // not used
        
       // $dark = "#118552";
    
        if (!empty($color)) {
           // $pale = "#".$color;
           // $base = "#".FormatHelper::getNeighborColor($color , true);
           // $dark = "#".FormatHelper::getNeighborColor($color , true);
        }
    
        return array( "base" => $base, "pale" => $pale, "dark" => $dark);
    }    
    
    public function getCurrentPageId() {
        $pageId =  $this->request->path();
        //Log::debug("PATH: $pageId");
        $pageId = str_replace("Submit", "", $pageId);
       // $pageId = self::changeVcGroupPageId($pageId);

        return $pageId;
    }
    
    /*
    static function changeVcGroupPageId($pageId) {
       $datas = Rdb::getVcGroup();
       
       foreach ($datas as $data) {
           if ($pageId == $data['routeName']."/add") {
               return "vehicleCheck/add";
           }    
           if ($pageId == $data['routeName']."/edit") {
               return "vehicleCheck/edit";
           }      
           if ($pageId == $data['routeName']."/view") {
               return "vehicleCheck/view";
           }      
       }
       return $pageId;
    }
    */
    
    /**
     * @param String Ex. "(\$Date: 2013-07-11 18:12:09 +0700 (พฤ., 11 ก.ค. 2013) $)"
     */
    function formatBuildDate($data) {
        $day = substr($data, 16, 2);
        $month = substr($data, 13, 2);
        $year = substr($data, 8, 4);
        $time = substr($data, 19, 8);
    
        $year = intval($year);
        if ($year > 3000) { // มีบั้กจากเอา svn รุ่นใหม่มาใช้  "$Date$" มันได้ปีผิดเช่น ปี 2016 กลายเป็น 3645
            $year = $year-1629;
        }        
        return "$day/$month/$year $time";
    }
    
    /**
     * @param String Ex. "\$Revision: 107 $"
     */
    function formatBuildRevision($data) {
        preg_match('!\d+!', $data, $matches);
        return ( sizeof($matches) > 0 ) ? $matches[0] : "-";
    }
        
    function getPageBuildDate() {
        $buildDate = "";
        $filename = public_path().DIRECTORY_SEPARATOR.'revision.txt';
        //myDebug($filename);
        
        if (file_exists($filename)) {
            $time = filemtime($filename);
            $buildDate = DateHelper::timeToThai($time);
        }
        return $buildDate;
    }
    

    
}    


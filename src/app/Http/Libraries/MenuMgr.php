<?php

namespace App\Http\Libraries;

use Log;
use App\Http\Models\User;
use App;
use URL;
use App\Http\Models\Rdb;

class MenuMgr 
{       
    private $menuItems;
    private $shortcutItems;
    
    public function __construct($app) {
        
    }
    
    public function getMenu($isTopMenu = true) {
        $this->menuItems = array();
        $this->shortcutItems = array();
        
        $alarm_desc = "";        
        $menu = "";

        if (Rdb::$ACCOUNT_SYSADMIN == App::make('AuthMgr')->getLoginUserId()) {
            $this->getSysadminMenu($isTopMenu, "");
        }
        else {
            $this->getDefaultMenu($isTopMenu, "");
        }
                
        
        if ($isTopMenu) {
            $menu  .= "<div class='nestedsidemenu'><ul>";
            $menu .= $this->renderMenuText($isTopMenu);
            $menu .= "</ul></div>";            
        }
        else {
            $menu  .= "<ul class='mainmenu'>";
            $menu .= $this->renderMenuText($isTopMenu);
            $menu .= "</ul>";               
        }
                


        // shortcut
        if ($isTopMenu) {
            $menu .= $this->renderShortcut();
        }
        $menu .= "<div style='clear:both'></div>";
        
        return $menu;
    }
    

    private function getSysadminMenu($is_top_menu, $alarm_desc) {

        // Main
        $this->addTopMenu("", "เมนูหลัก",  array('id' => "appMenuMain") );
        $this->addMenu( "home","หน้าแรก" );
        $this->addMenu( "logout","ออกจากระบบ" );
    
        
        // Administrator
        $this->addTopMenu("", "จัดการข้อมูลระบบ",  array('id' => "appMenuSystem"));
        $this->addMenu( "account/index","ข้อมูลบัญชี" );
        $this->addMenu( "userGroup/index","ข้อมูลกลุ่มผู้ใช้" );
        $this->addMenu( "user/index","ข้อมูลผู้ใช้" );
        $this->addMenu( "user/changePassword","เปลี่ยนรหัสผ่าน" );
          
    }
    
    private function getDefaultMenu($is_top_menu, $alarm_desc) {
        $menuNormalWidth = 350;
        
         // Main
        $this->addTopMenu("", "เมนูหลัก",  array('id' => "appMenuMain") , array("width" => $menuNormalWidth));
        $this->addMenu( "home","หน้าแรก" );
        $this->addMenu( "logout","ออกจากระบบ" );
  

        // Vehicle
        $this->addTopMenu("", "จัดการรถขนส่ง",  array('id' => "appMenuVehicle") , array("width" => $menuNormalWidth));
        $this->addMenu( "vehicle/index","ขึ้นทะเบียนรถขนส่ง");   
        $this->addMenu( "vehInsurance/index","จัดการข้อมูลประกันภัย");           
        $this->addMenu( "vehAccident/index","จัดการอุบัติเหตุ-ความเสียหาย");  
        $this->addMenu( "vehRepair/index","จัดการการซ่อมบำรุง");   
       
        // Staff
        $this->addTopMenu("", "จัดการพนักงาน",  array('id' => "appMenuStaff") , array("width" => $menuNormalWidth));
        $this->addMenu( "staff/index","ขึ้นทะเบียนพนักงาน");   

        // Alarm
        $this->addTopMenu("", "แจ้งเตือน",  array('id' => "appMenuAlarm") , array("width" => $menuNormalWidth));
        $this->addMenu( "alarmLog/index","รายการแจ้งเตือน");   
        $this->addSeperator();
        $this->addMenu( "monitorTopic/index","หัวข้อการซ่อมบำรุง");   
        $this->addMenu( "monitorPlan/index","แผนการซ่อมบำรุง");   
        
        
        // Administrator
        $this->addTopMenu("", "จัดการข้อมูลระบบ",  array('id' => "appMenuSystem"), array("width" => $menuNormalWidth));        
        $this->addMenu( "userGroup/index","กลุ่มผู้ใช้" );
        $this->addMenu( "user/index","ผู้ใช้" );       
        $this->addMenu( "user/changePassword","เปลี่ยนรหัสผ่าน" );
        $this->addMenu( "appSetting/index","ตั้งค่าใช้งานระบบ" );       
        $this->addMenu( "appSettingForUser/index","ตั้งค่าใช้งานระบบสำหรับผู้ใช้" );   
        $this->addMenu( "alarmSetting/index","หน้าตั้งค่างานแจ้งเตือน" );     
        
        $this->addSeperator();
        $this->addMenu( "goodsContainer/index","ชนิดตู้สินค้า" );    
        $this->addMenu( "refrigerant/index","ชนิดสารทำความเย็น" );    
        $this->addMenu( "vehicleCare/index","ผู้ให้บริการ" );    
        $this->addMenu( "vCareType/index","ชนิดรถให้บริการ" );              
        $this->addMenu( "workCompany/index","สังกัด" ); 
        
        $this->addMenu( "staffType/index","ชนิดพนักงาน" ); 
        $this->addMenu( "licenseType/index","ประเภทใบขับขี่" );         
        
        
        // UserManual
        $this->addTopMenu("", "คู่มือการใช้งาน",  array('id' => "appMenuUserManual") , array("width" => $menuNormalWidth));
        $this->addMenu( "userManual/index","คู่มือการใช้งาน");   
        
        // Background
       // $this->addTopMenu("", "งานเบื้องหลัง",  array('id' => "appMenuBackground") );
      //  $this->addMenu( "appSetting/index","ตั้งค่าใช้งานระบบ");


    }
    
    //================================================================
       
    
    private function addTopMenu($page_id, $text, $attribute = array(), $options = array()) {
        $top_menu = new \stdClass();
        $top_menu->page_id = $page_id;
        $top_menu->text = $text;
        $top_menu->attribute = $attribute;
        $top_menu->menu_no =  (isset($attribute['id']))? App::make('PageFactory')->getMenuNo($attribute['id']):"";        
        $top_menu->datas = array();
        $top_menu->width = isset($options['width'])? $options['width']: 0;
        
        $this->menuItems[] = $top_menu;
    }
    
    private function addMenu($page_id, $text, $width = null, $attribute = array()) {        
        $menu = new \stdClass();
        $menu->page_id = $page_id;
        $menu->text = $text;
        $menu->attribute = $attribute;
        $menu->page_no =  App::make('PageFactory')->getPageNo($page_id);
        $menu->type = "item";
        
        $page =  App::make('PageFactory')->getPage($page_id); 
        
        //DataHelper::debug($page_id);
        
        if ($page && App::make('AuthMgr')->hasPagePermission($page_id) ) { 
            
            //DataHelper::debug("OKK");
            
            $index = sizeof($this->menuItems)-1;
            $this->menuItems[ $index ]->datas[] = $menu;
            
            if (!empty($width) && $width > $this->menuItems[$index ]->width) {
                $this->menuItems[$index]->width = $width;
            }
            
        }
        
    }
    
    
    private function addShortcut($page_id, $text, $icon) {
        $shortcut = new \stdClass();
        $shortcut->page_id = $page_id;
        $shortcut->text = $text;
        $shortcut->icon = $icon;
        
        $page =  App::make('PageFactory')->getPage($page_id);
    
        if ($page && App::make('AuthMgr')->hasPagePermission($page_id) ) {
            $this->shortcutItems[] = $shortcut;
        }    
    }
    
    
    private function addSeperator() {
        $menu = new \stdClass();
        $menu->type = "seperator";
        
        $index = sizeof($this->menuItems)-1;
        $this->menuItems[ $index ]->datas[] = $menu;        
    }
    
    private function addNewBlock() {
        $menu = new \stdClass();
        $menu->type = "new_block";
        
        $index = sizeof($this->menuItems)-1;
        $this->menuItems[ $index ]->datas[] = $menu;        
    }
    
    private function addScriptLink($script, $text) {
        $menu = new \stdClass();
        $menu->type = "script_link";
        $menu->script = $script;
        $menu->text = $text;
        
        $index = sizeof($this->menuItems)-1;
        $this->menuItems[ $index ]->datas[] = $menu;        
    }    
    
    private function clearEmptyTopMenu() {
        $new_menu = array();
        foreach ($this->menuItems as $top_menu) {
            if ( $this->getMenuItemSize($top_menu->datas)  > 0 || 
                ( !empty($top_menu->page_id) &&  App::make('AuthMgr')->hasPagePermission($top_menu->page_id) )
            ) {
                $new_menu[] = $top_menu;
            }
        }
        
        return $new_menu;
    }
    
    private function getMenuItemSize($menus) {
        $count = 0;
        if (!empty($menus)) {            
            foreach ($menus as $menu) {
                if ($menu->type  !=  "new_block" && $menu->type  !=  "seperator" ) {
                    $count++;
                }
            }
        }
        return $count;
        
    }
    
    private function renderMenuText($isTopMenu) {
        $output = "";
        $menuItems = $this->clearEmptyTopMenu();
        
        for ($i = 0; $i < sizeof($menuItems); $i++) {
            $top_menu = $menuItems[$i];   
            $link = (empty($top_menu->page_id))? '#' :  URL::to("/".$top_menu->page_id);
            $attribute = $top_menu->attribute;
            $text = $top_menu->text;
            $menus = $top_menu->datas;
            $size = sizeof($top_menu->datas);            
            $menu_no = $top_menu->menu_no;
            $width = $top_menu->width;
            $text =  !empty($menu_no)?  $menu_no.". ".$text: $text;
                        
            if ($i == 0) {
                $attribute['class'] = "firstItem";
            }
            else if ($i == sizeof($menuItems)-1) { // last item
                $attribute['class'] = "lastItem";
            }
            
            //DataHelper::debug($text." / ".$size." / ".$menu_no);
            //---------------------------------------
            
            if (empty($link)) {
                $output .= '<li><a href="#non" >'.$text.'</a>';
            }
            else {
                $output .= '<li>'.SiteHelper::anchor($link, $text, $attribute );
            }
              
            $prevMenuType = null;            
            $output .= ($size > 0)? "<ul>":"";
            
            $menuCount = 0;            
            foreach ($menus as $menu) {
                $menuCount++;
                
                if ($menu->type  == "new_block"  && $isTopMenu &&  $prevMenuType != "new_block" && $menuCount > 1) {
                    $output .= "</ul><ul style='left:".$width."px' >";
                }
                else if ($menu->type  == "seperator"  && $isTopMenu &&  $prevMenuType != "seperator" && $menuCount > 1) {
                    $output .=  "<li><div style='background-color:#CCC; height: 1px; font-size:0px; width:".$width."px'></div></li>";
                }
                else if ($menu->type  == "script_link"  && $isTopMenu) {
                    $output .=  "<li><a href='javascript:void(0);' onclick=\"".$menu->script." \"  >".$menu->text."</a></li>";
                }                
                else if (property_exists($menu, "page_no") ){
                    $width_str = (!empty($width) && $width>0)? "style='width:".$width."px'": "";
                    $menu_text = $menu->page_no." ".$menu->text;
                    $href = URL::to("/".$menu->page_id);
                    $output .= '<li '.SiteHelper::_parseAttributes($menu->attribute).'  '.$width_str.'>'. SiteHelper::anchor($href, $menu_text).'</li>';
                }

                $prevMenuType = $menu->type;
            }

            $output .= ($size > 0)? "</ul>":"";
            $output .= "</li>";

        }

        return $output;
    }
    
    private function renderShortcut() {
        $output = "";
        
        $shortcuts = $this->shortcutItems;
        
        //DataHelper::debug($shortcuts);
        
        if (sizeof($shortcuts) > 0) {
  
            $output .= "";
            foreach ($shortcuts as $shortcut) {
                $output .= "<div style='float:left; font-size:12px; padding:3px; font-weight:bold'>";
                $icon = URL::asset($shortcut->icon);
                $link = URL::to("/".$shortcut->page_id);
                $img = "<img src='$icon' style='width:16px; height:16px; position: relative; top: -2px;' />";
                 
                $text = $shortcut->text;
            
                $output .= $img;
                $output .= SiteHelper::anchor($link, $text, array("style"=>"color:black; padding:3px") ); //
            
                $output .= "</div>";
            }         
          
        }
      
        $this->shortcutItems = array();
        
        return $output;
    }
}

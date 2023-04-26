<?php 

namespace App\Http\Models;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Models\Core\DataTable;
use App\Http\Libraries\SqlHelper;
use App\Http\Libraries\DataHelper;
use App;

class UserGroupPagePermission extends MyBaseModel
{    
    private static $userGroupArray;
    
    public static function getDataTable($request, $userGroupId) { // , $appPlanId
        
        $datas =  self::getDatas($userGroupId); // , $appPlanId
        $dataRows = array();
        
        $order = 1;
        $currentMenu = "";
        
        foreach ($datas as $data) {
        
            $type = (in_array( App::make("PageFactory")->propertyMainPage , $data->property))? "main":"";
        
            if ($data->menu != $currentMenu) {
                $dataRows[] = array(
                        "order"=> "",
                        "name"=> App::make("PageFactory")->getMenuDesc($data->menu),
                        "description"=> "เมนูหลักของระบบ",
                        "permission"=> "",
                        "page_id" => "",
                        "type" => "menu",
                        "mode_opt" => "",
                        "mode" => "",
                );
                $currentMenu = $data->menu;
            }
        
            $dataRows[] = array(
                    //$idx++,
                    "order"=> $order++,
                    "name"=> $data->name,
                    "description"=> $data->description,
                    "permission"=> $data->permission,
                    "page_id"=> $data->id,
                    "type"=> $type,
                    "mode_opt" => $data->mode_opt,
                    "mode" => $data->mode,
            );
        }
        return $dataRows;
    
    }
 
    public static function  getDatas($userGroupId) { // , $appPlanId
        if (empty($userGroupId)) return array();
    
        $pages = App::make("PageFactory")->getPageDatas(); // $appPlanId
    
        $query = DB::table('user_group_page_permission');
        $query->where("userGroupId", $userGroupId);
       // $query->where("app_plan_id", $appPlanId);

        $permissionDatas = $query->get();

    
        foreach ($pages as &$page) {
            $permission =  self::getPermissionOfPage( $page->id , $permissionDatas );
    
            $page->permission = ($permission)? $permission['permission']: 0;
            $page->mode = ($permission)? $permission['mode']: "";
        }
    
        return $pages;
    }
    
    private static function getPermissionDatas($userGroupId) { // , $appPlanId
        if (empty($userGroupId)) { //  || empty($appPlanId)
            return array();
        }
    
        $query = DB::table('user_group_page_permission');
        $query->where("userGroupId", $userGroupId);
       // $query->where("app_plan_id", $appPlanId);        
        $output = $query->get();
        return $output;
    }
    
    private static function getPermissionOfPage($pageId , $permissionDatas) {
        foreach ($permissionDatas as $data) {
            if ($data['page'] == $pageId) {
                return $data;
            }
        }
        return false;
    }
    
    public static function hasPermission($userGroupId, $pageId) { // , $appPlanId 
        if (empty($userGroupId) || empty($pageId)) { //  || empty($appPlanId)
            return false;
        }           
        
        $datas = self::getPermissionDatas($userGroupId); // , $appPlanId
        foreach ($datas as $data) {
            if ($data['page'] == $pageId) {
                return ($data['permission'])? true : false;
            }
        }
    
        return false;
    }    
    
    //*********************************************************************************************
    //
    //  CREATE, UPDATE
    //
    //*********************************************************************************************
    
    
    public static function updateData($userGroupId,  $resultDatas) { // , $appPlanId
        $dataRows = array();    

        foreach ($resultDatas as $data) {
            $pageId = $data->page_id;
            $permission = $data->permission;
            $mode = $data->permission_mode;
    
            if (!empty($pageId)) {
                $dataRows[] = array(
                        "userGroupId" => $userGroupId,
                      //  "app_plan_id" => $appPlanId ,
                        "page" => $pageId,
                        "permission" => $permission,
                        "mode" => $mode,
                );
            }
        }

        self::deleteData($userGroupId); // , $appPlanId
        DB::table('user_group_page_permission')->insert($dataRows);

        return true;
    }
    
    public static function deleteData($userGroupId) {         // , $appPlanId
        $query = DB::table('user_group_page_permission');
        $query->where("userGroupId", $userGroupId);
       // $query->where("app_plan_id", $appPlanId);             
        $query->delete();
    }
    
}



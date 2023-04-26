<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\UserGroup\UserGroupController;
use App\Http\Controllers\Account\AccountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|

Route::get('/', function () {
    return view('welcome');
});
*/

if (! function_exists('addRouteGet')) {
    function addRouteGet($linkPrefix, $targetClass, $nameList, $middleware=null) {
        foreach ($nameList as $name) {
           // $route = Route::get("/$linkPrefix/$name", "$conPrefix@$name");
           // $route = Route::get("/$linkPrefix/$name", "$conPrefix@$name");
            $route = Route::get("/$linkPrefix/$name", [$targetClass, $name]);
         
            if (!empty($middleware)) {
                $route->middleware($middleware);
            }
        }
    }
}

if (! function_exists('addRoutePost')) {
    function addRoutePost($linkPrefix, $targetClass, $nameList, $middleware=null) {
        foreach ($nameList as $name) {
            //$route = Route::post("/$linkPrefix/$name", "$conPrefix@$name");
            
            $route = Route::post("/$linkPrefix/$name", [$targetClass, $name]);
             
            if (!empty($middleware)) {
                $route->middleware($middleware);
            }        
        }
    }
}

if (! function_exists('addRouteGetAndPost')) {
    function addRouteGetAndPost($linkPrefix, $conPrefix, $nameList, $middleware=null) {
        addRouteGet($linkPrefix, $conPrefix, $nameList, $middleware);
        addRoutePost($linkPrefix, $conPrefix, $nameList, $middleware);
    }
}

//========================================================================
// Home

Route::get('/', [HomeController::class, 'index']);
Route::get('/home', [HomeController::class, 'index']);
Route::get('/login', [LoginController::class, 'index']);
Route::post('/login', [LoginController::class, 'doLogin']);
Route::get('/logout', [LoginController::class, 'doLogout']);
Route::get('/selectBranch', [LoginController::class, 'selectBranch']);


//========================================================================
// Vehicle

addRouteGet("vehicle", \App\Http\Controllers\Vehicle\VehicleController::class, array("index", "view"));
addRoutePost("vehicle", \App\Http\Controllers\Vehicle\VehicleController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("vehInsurance", \App\Http\Controllers\Vehicle\VehInsuranceController::class, array("index", "view"));
addRoutePost("vehInsurance", \App\Http\Controllers\Vehicle\VehInsuranceController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("vehAccident", \App\Http\Controllers\Vehicle\VehAccidentController::class, array("index", "view"));
addRoutePost("vehAccident", \App\Http\Controllers\Vehicle\VehAccidentController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("vehRepair", \App\Http\Controllers\Vehicle\VehRepairController::class, array("index", "view"));
addRoutePost("vehRepair", \App\Http\Controllers\Vehicle\VehRepairController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));


//========================================================================
// Staff

addRouteGet("staff", \App\Http\Controllers\Staff\StaffController::class, array("index", "view"));
addRoutePost("staff", \App\Http\Controllers\Staff\StaffController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable","getPopupDataTable"));



//========================================================================
// Alarm

addRouteGet("alarmLog", \App\Http\Controllers\Alarm\AlarmLogController::class, array("index", "view"));
addRoutePost("alarmLog", \App\Http\Controllers\Alarm\AlarmLogController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable",    
        "getDataTableForDashboard","getPopupDataTable","updateAckBy"));


addRouteGet("monitorTopic", \App\Http\Controllers\Alarm\MonitorTopicController::class, array("index", "view"));
addRoutePost("monitorTopic", \App\Http\Controllers\Alarm\MonitorTopicController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable","getPopupDataTable"));


addRouteGet("monitorPlan", \App\Http\Controllers\Alarm\MonitorPlanController::class, array("index", "view"));
addRoutePost("monitorPlan", \App\Http\Controllers\Alarm\MonitorPlanController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable","getPopupDataTable"));



//===================================================================
// Report



//========================================================================
// System

addRouteGet("account", AccountController::class, array("index"));
addRoutePost("account", AccountController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("user", UserController::class, array("index","changePassword"));
addRoutePost("user", UserController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable",
        "viewSiteUsageHistory","getSiteUsageHistoryDataTable","changePasswordSubmit"));

addRouteGet("userGroup", UserGroupController::class, array("index"));
addRoutePost("userGroup", UserGroupController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable",
        "viewPagePermission","editPagePermission","editPagePermissionSubmit","getPagePermissionDataTable" ));


addRouteGet("appSetting", \App\Http\Controllers\AppSetting\AppSettingController::class, array("index"));
addRoutePost("appSetting", \App\Http\Controllers\AppSetting\AppSettingController::class, array(
        "edit","editSubmit","view","getDataTable"));

addRouteGet("appSettingForUser", \App\Http\Controllers\AppSetting\AppSettingForUserController::class, array("index"));
addRoutePost("appSettingForUser", \App\Http\Controllers\AppSetting\AppSettingForUserController::class, array(
        "edit","editSubmit","view","getDataTable"));

addRouteGet("alarmSetting",  \App\Http\Controllers\Alarm\AlarmSettingController::class, array("index","edit"));
addRoutePost("alarmSetting", \App\Http\Controllers\Alarm\AlarmSettingController::class, array(
        "add","addSubmit","edit","editSubmit","view","getDataTable"));

//-----------------------------------------------------------------------------------


addRouteGet("goodsContainer", \App\Http\Controllers\Common\GoodsContainerController::class, array("index"));
addRoutePost("goodsContainer", \App\Http\Controllers\Common\GoodsContainerController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("refrigerant", \App\Http\Controllers\Common\RefrigerantController::class, array("index"));
addRoutePost("refrigerant", \App\Http\Controllers\Common\RefrigerantController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("vehicleCare", \App\Http\Controllers\Common\VehicleCareController::class, array("index"));
addRoutePost("vehicleCare", \App\Http\Controllers\Common\VehicleCareController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("vCareType", \App\Http\Controllers\Common\VCareTypeController::class, array("index"));
addRoutePost("vCareType", \App\Http\Controllers\Common\VCareTypeController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("gasTankSetting", \App\Http\Controllers\Common\GasTankSettingController::class, array("index"));
addRoutePost("gasTankSetting", \App\Http\Controllers\Common\GasTankSettingController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("workCompany", \App\Http\Controllers\Common\WorkCompanyController::class, array("index"));
addRoutePost("workCompany", \App\Http\Controllers\Common\WorkCompanyController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("staffType", \App\Http\Controllers\Common\StaffTypeController::class, array("index"));
addRoutePost("staffType", \App\Http\Controllers\Common\StaffTypeController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));

addRouteGet("licenseType", \App\Http\Controllers\Common\LicenseTypeController::class, array("index"));
addRoutePost("licenseType", \App\Http\Controllers\Common\LicenseTypeController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));


//========================================================================
// USER MANUAL


addRouteGet("userManual", \App\Http\Controllers\Common\UserManualController::class, array("index"));
addRoutePost("userManual", \App\Http\Controllers\Common\UserManualController::class, array(
        "add","addSubmit","edit","editSubmit","view","delete","getDataTable"));



//=============================================================
// UPLOAD

addRoutePost("fileUpload", \App\Http\Controllers\FileUpload\FileUploadController::class, array("upload"));
addRouteGet("fileUpload", \App\Http\Controllers\FileUpload\FileUploadController::class, array("view"));



//=============================================================
// SERVICE


addRouteGet("testService", App\Http\Controllers\Service\TestServiceController::class, 
        array("hello", "getToken", "showToken","addStrip", "getPlanList", "updateDealer"));

addRoutePost("testService", App\Http\Controllers\Service\TestServiceController::class, 
        array("hello2"));

addRouteGetAndPost("authService", App\Http\Controllers\Service\AuthServiceController::class, 
   array("hello", "getToken", "isTokenValid", "showToken", "getTokenByLogin", "getStartUpData"));

addRouteGetAndPost("commonService", App\Http\Controllers\Service\CommonServiceController::class,
  array("getStaffData", "getDriverData", "addImage", "viewImage" , "deleteImage", "addFile", "viewFile",
      "getVehicleData", "getVehicleIdList", "downloadImage", "getHealthCheckAppInfo", "getVehicleCheckAppInfo"
    ), 'simpleTokenApiAuth');


//=============================================================
// PROCESS


addRouteGet("testProcess", App\Http\Controllers\Process\TestProcessController::class, 
        array("hello", "addImage", "testPdf1", "testPdf2", ));

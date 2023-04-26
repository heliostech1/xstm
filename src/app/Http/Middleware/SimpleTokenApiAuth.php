<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Requests;
use Illuminate\Http\Request;
use App;
use Log;
use App\Http\Libraries\DataHelper;
use Validator;
use App\Http\Models\Rdb;
use App\Http\Models\User;
use App\Http\Models\AppSetting\AppSetting;

class SimpleTokenApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //if (app()->environment('local')) {
            $log = [
                'URI' => $request->getUri(),
                'METHOD' => $request->getMethod(),
                'REQUEST_BODY' => $request->all(),
            ];

            Log::channel('myApi')->info(json_encode($log));
        //}
        
        
        $result = $this->checkAuth($request);
        if ($result !== true) {
            return $this->myJsonResponse(array("message" => $result), Rdb::$HTTP_CODE_AUTH_FAIL);
        }
        
        return $next($request);
    
    }
    
    public function checkAuth($request) {
        $token = $request->input("token");
        $appSetting = AppSetting::getData(Rdb::$APP_SETTING_API_TOKEN);
        $appToken = getMyProp($appSetting, 'value', '');
        
        if (empty($appToken)) {
            return "ไม่พบการตั้งค่าสิทธิ์การใช้";
        }
        
        if ($appToken != $token ) {
            return "ข้อมูลสิทธิ์การใช้ไม่ถูกต้อง";
        }
        return true;
    }
    
    public function myJsonResponse($json = [], $status = 200) {
        $headers = ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'];
        
        return response()->json($json, $status, $headers, JSON_UNESCAPED_UNICODE);
    }

}



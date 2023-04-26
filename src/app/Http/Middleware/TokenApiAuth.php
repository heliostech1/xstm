<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Requests;
use Illuminate\Http\Request;
use App;
use Log;
use App\Http\Libraries\DataHelper;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Http\Models\Rdb;
use App\Http\Models\User;

class TokenApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        $result = $this->checkAuth();
        if ($result !== true) {
            return response()->json(array("message" => $result), Rdb::$HTTP_CODE_AUTH_FAIL);
        }
        
        return $next($request);
    
    }
    
    public function checkAuth() {
        $applyAuth = config('app.restApplyAuth');
        if (!$applyAuth) {
            return true;
        }
        
        $payload = null;
    
        JWTAuth::getToken();
    
        try {
            $payload = JWTAuth::getPayload();
    
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return "[token_expired]";
    
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return "[token_invalid]";
    
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return "[token_absent]";
        }
        //DataHelper::debug($payload);
    
  
        $accountId = $payload->get('accountId');
        $user = $payload->get('username');
        $pass = $payload->get('password');

    
        if (! User::checkUserPassword($user,$pass,$accountId ) ) {
            return "ข้อมูลผู้ใช้หรือรหัสผ่านไม่ถูกต้อง [invalid_username_password]";
        }
        
        return true;
    }
    
    
}



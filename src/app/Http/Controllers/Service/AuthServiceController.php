<?php
namespace App\Http\Controllers\Service;

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
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Http\Models\Rdb;
use App\Http\Models\User;
use App\Http\Models\Branch;
use App\Http\Models\Box\Box;

/**
 * SWAGGER
 * -> SPEC: http://swagger.io/specification/
 * -> EDITOR: http://editor.swagger.io/#/
 * -> UI: http://petstore.swagger.io/
 * 
 * JWT (Api authorize)
 * -> https://github.com/tymondesigns/jwt-auth/wiki/Authentication
 *
 */

class AuthServiceController extends BaseServiceController
{
    public function __construct() {
        //parent::__construct();
    }
        
    public function hello(Request $request) {        
        return response()->json("I'm AuthServiceController");
    }    
    
    /** http://192.168.1.55/xxxx/public/authService/isTokenValid?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJhY2NvdW50X2lkIjoidGVzdCIsInVzZXJuYW1lIjoiYWRtaW5pc3RyYXRvciIsInBhc3N3b3JkIjoicGFzc3dvcmQiLCJpc3MiOiJodHRwOlwvXC8xOTIuMTY4LjEuNTVcL2hlbGlvc19jZG1zXC9wdWJsaWNcL2F1dGhTZXJ2aWNlXC9nZXRUb2tlbkJ5TG9naW4iLCJpYXQiOjE2MTA2MzM5MDYsImV4cCI6MTY3MDYzMzkwNiwibmJmIjoxNjEwNjMzOTA2LCJqdGkiOiJURGFTRVlneUQzaGF1NWtOIn0.ie94zdRhfYkcXa_dTgNf2glGd0lfNH2qYQ377lThNPg
  */
     public function isTokenValid() {
     
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
        
        //myDebug($payload);
        
        $accountId = $payload->get('accountId');
        $user = $payload->get('username');
        $pass = $payload->get('password');
        //$appUser = config('app.restUsername');
        //$appPass = config('app.restPassword');
        
       // DataHelper::debug("$user | $pass");
        
        if (! User::checkUserPassword($user , $pass, $accountId ) ) {
            return "[user_password_invalid]";   
        }
        
        return true;
    }
    
    /** http://192.168.1.15/xxxx/public/authService/getTokenByLogin?accountId=test&username=administrator&password=password */
    public function getTokenByLogin(Request $request) {

        $authMgr = App::make('AuthMgr');
        $authMgr->setSession($request);
        
        $reqAccountId = $request->input('accountId');        
        $reqUser = $request->input('username');
        $reqPass = $request->input('password');
       
        
        $errors = array();
        
        if (empty($reqAccountId) || empty($reqUser) || empty($reqPass)) {
            $errors[] = "ระบุข้อมูลไม่ครบถ้วน [invalid_parameter]";
        }      
        else if ( !$authMgr->doLogin($reqAccountId,$reqUser,$reqPass ) ) {
            $loginErrors = $authMgr->getErrors();
            if (sizeof($loginErrors) > 0) {
                $errors = $loginErrors;
            }
            else {
                $errors[] =  "ข้อมูลผู้ใช้หรือรหัสผ่านไม่ถูกต้อง [invalid_username_password]";
            }
            
        }
        
        if (sizeof($errors) > 0) {
            return response()->json(array('message' => implode(',',$errors)), Rdb::$HTTP_CODE_ERROR);
        }

       // $customClaims = ['accountId' => $reqAccountId, 'username' => $reqUser, 'password' => $reqPass];
       // $payload = JWTFactory::make($customClaims);

        $payload = JWTFactory::accountId($reqAccountId)->username($reqUser)->password($reqPass)->make();


        //myDebug($payload);
        
        $token = JWTAuth::encode($payload);

        $compactToken = compact('token');
        //DataHelper::debug($compactToken);
        
        return response()->json( array('result' => $token->get()), Rdb::$HTTP_CODE_OK );
    }
    
    
    /** http://localhost/xxxx/public/authService/getStartUpData?token= */

    public function getStartUpData() {
        $retData = array();
        $isTokenValid = $this->isTokenValid();        
        
        $retData['is_token_valid'] = ($isTokenValid === true)? "Y": "N";
        $retData['is_token_valid_desc'] = ($isTokenValid);        

        //$branchs = Branch::getAllDataArray();
        
        $retData['branch_options'] = array(
                array( "value" => "", "text" => "..."),                            
        );        
        $retData['box_options'] = array(
                array( "value" => "", "text" => "..."),                            
        );  
        $retData['order_status_options'] = array(
                array( "value" => Rdb::$SHOPEE_ORDER_STATUS_READY_TO_SHIP, "text" => "Ready To Ship"),         
                array( "value" => Rdb::$SHOPEE_ORDER_STATUS_ALL, "text" => "All"),     
        );     
         
        
        return response()->json(array('result' => $retData), Rdb::$HTTP_CODE_OK);
        
    }
    
}




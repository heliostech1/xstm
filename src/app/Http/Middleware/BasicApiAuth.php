<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Libraries\DataHelper;

class BasicApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        $username = "";
        $password = "";
    
        if (isset($_SERVER['PHP_AUTH_USER'])) { // case: login dialog
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
        }
        else if (isset($_SERVER['HTTP_AUTHENTICATION'])) { // case: url
            list($username, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
        }
    
        if (!$this->checkUserPass($username, $password)) {

            header('WWW-Authenticate: Basic realm="REST API ZZZ"'); // มีนี้ มี 401 ถึงได้ ??????????
            
            return response()->json(array("status" => false, "error" => 'Not authorized EEE'), 401);
                 //->header('WWW-Authenticate: Basic realm', 'rest api')
                
            
        }
                
        return $next($request);
    
    }
    
    protected function checkUserPass($username = '', $password = false)
    {
        //DataHelper::debug("CHECK: $username , $password ");
        
        if (empty($username) || empty($password)) {
            return false;
        }
    
        if ($username != "tee" && $password != "dee") {
            return false;
        }
    
        //.............
        return true;
    }
    
}

<?php

namespace App\Http\Controllers\Service;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use App;
use Log;
use App\Http\Libraries\DataHelper;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;

class BaseServiceController extends Controller
{
    public function __construct() {
       // parent::__construct();

       // return response()->json("EESS")->send();
    }
    
    
    public function myJsonResponse($json = [], $status = 200) {
        $headers = ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'];
        
        return response()->json($json, $status, $headers, JSON_UNESCAPED_UNICODE);
    }

}    




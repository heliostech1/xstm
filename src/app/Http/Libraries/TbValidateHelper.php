<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use App;

class TbValidateHelper extends MyBaseLib
{         
    public static function test($test) {
        return "TEST: $test";
    }
    
    public static function debug($object, $prefix="") {
        static::debugObject($object, $prefix);
    }
    
    public static function debugObject($object, $prefix="") {
        $result = var_export($object, true);
        Log::debug($prefix."::".$result);
    }

}


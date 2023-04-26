<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use App;
use Validator;

class MyBaseLib
{

    public function __construct()
    {
        
    }

    //===============================================================================
    //
    // PART: ERROR
    //
    //===============================================================================
    
    protected static $errors = array();
        
    public static function setError($error) {
        if (is_array($error)) {
            self::$errors = array_merge(self::$errors, $error);
        }
        else {
            self::$errors[] = $error;
        }
         
        //DataHelper::debug(self::$errors, "SET ERROR: $error");
        return $error;
    }
    
    public static function hasErrors() {
        if (sizeof(self::$errors) > 0) {
            return true;
        }
        false;
    }
    
    public static function getErrors() {
        $errors = self::$errors;
        static::clearErrors();
        return $errors;
    }
    
    public static function clearErrors() {
        self::$errors = array();
    }
    
    public static function errors() {
        $output = '';
        foreach (self::$errors as $error) {
            $output .= FormatHelper::wrapInfoDiv($error);
        }
    
        static::clearErrors();
        return $output;
    }
    
    public static function errorsPlainText() {
        $output = implode(",", self::$errors);
        static::clearErrors();
        return $output;
    }    

}



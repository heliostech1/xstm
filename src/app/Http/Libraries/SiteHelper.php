<?php

namespace App\Http\Libraries;

use Closure;
use Log;
use DB;
use URL;
use App;
use App\Http\Models\Rdb;

class SiteHelper extends MyBaseLib
{  
    
    /**
     *  บังคับไม่ให้ browser เอาไฟล์จาก cache มาใช้ เมื่อเอางานเวอร์ขั่นใหม่ขึ้น
     *  http://stackoverflow.com/questions/118884/what-is-an-elegant-way-to-force-browsers-to-reload-cached-css-js-files
     *  ( มีปรับโค้ดให้ใช้กับ param ที่เป็น relative path )
     *
     *  ใช้คู่กับ
     *  RewriteEngine on
     *  RewriteRule ^(.*)\.[\d]{10}\.(css|js)$ $1.$2 [L]
     *
     *  --------------------
     *
     *  Given a file, i.e. /css/base.css, replaces it with a string containing the
     *  file's mtime, i.e. /css/base.1221534296.css.
     *
     *  @param $file  The file to be loaded.  Must be an absolute path (i.e.
     *                starting with slash).
     */
    
    // format url path
    public static function url($url) {
       $url = static::autoVersion($url);  
       return URL::asset($url);
    }
    
    public static function autoVersion($file)
    {
        $base_path = dirname(dirname(dirname(dirname(__FILE__))));
        $abs_path = static::getAbsolutePath($file);
        
        $path = $base_path.DIRECTORY_SEPARATOR."public".DIRECTORY_SEPARATOR.$abs_path;
    
        //DataHelper::debug("PATH1: $base_path, PATH2: $abs_path,  IS_EXIST:".file_exists($path));
    
        if(!file_exists($path)) {
            return $file;
        }
    
        $mtime = filemtime($path);
        return preg_replace('{\\.([^./]+)$}', ".$mtime.\$1", $file);
    }    
    
    
    public static  function getAbsolutePath($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }    
    
    

    //===============================================================================
    //
    // HTML TAG
    //
    //===============================================================================
    
    public static function anchor($uri = '', $title = '', $attributes = '')
    {
        $title = (string) $title;
        $site_url = $uri;
    
        if ($attributes != '') {
            $attributes = static::_parseAttributes($attributes);
        }
    
        return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
    }
    
    public static function  _parseAttributes($attributes, $javascript = FALSE) {
        if (is_string($attributes)) {
            return ($attributes != '') ? ' '.$attributes : '';
        }
    
        $att = '';
        foreach ($attributes as $key => $val) {
            if ($javascript == TRUE) {
                $att .= $key . '=' . $val . ',';
            }
            else {
                $att .= ' ' . $key . '="' . $val . '"';
            }
        }
    
        if ($javascript == TRUE AND $att != '') {
            $att = substr($att, 0, -1);
        }
        return $att;
    }
    
    public static function footerBtnRight($page = '', $attributes = '') {
        return static::footerBtn($page , $attributes, 'right');
    }
    
    public static function footerBtn($page = '', $attributes = '', $side='', $sep = false) {
        //myDebug($page);
        if (!empty($page) && !App::make("AuthMgr")->hasPagePermission($page)) {
            return "";
        }
    
        $btnClass = ($side == 'right')? "footerBtnRight":"footerBtnLeft";
        $sep_html = ($sep)? "<span style='color:#AAA'> | &nbsp;</span>":"";
    
        $button = '<div class="'.$btnClass.'">'.$sep_html.'<input type="button" class="formButton" ';
        $button .=  $attributes;
        $button .= ' /></div>';
    
        return $button;
    }
    
    public static function checkPermission($page = '', $element = '') {
        if (!empty($page) && !App::make("AuthMgr")->hasPagePermission($page)) {
            return "";
        }
        return $element;
    }
    
    
    public static function dropdown($name = '', $options = array(), $selected = "", $extra = '') {

        if (empty($selected)) {
            if (isset($_POST[$name])) {
                $selected = array($_POST[$name]);
            }
        }

        $form = '<select name="'.$name.'"  '.$extra.">\n";
    
        foreach ($options as $key => $val) {
            $key = (string) $key;
    
            if (is_array($val) && !empty($val)) { // $option แบบใหม่รูปแบบ  array(key,  array("text"=> "xxx" , "active" => "Y");
                $text = getMyProp($val, "text", "");
                $active = getMyProp($val, "active", "");
                
                if ($key == $selected || $active == Rdb::$YES) {
                    $sel = ($key == $selected) ? ' selected="selected"' : '';
                    $form .= '<option value="'.$key.'"'.$sel.'>'.(string) $text."</option>\n";
                }
                
            }
            else {   // $option แบบเก่ารูปแบบ  array(key, value);
                $sel = ($key == $selected) ? ' selected="selected"' : '';
                $form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
            }
        }
    
        
        $form .= '</select>';
        return $form;
    }
    
    
    /*
    public static function dropdown($name = '', $options = array(), $selected = array(), $extra = '') {
        if ( ! is_array($selected)) {
            $selected = array($selected);
        }
    
        // If no selected state was submitted we will attempt to set it automatically
        if (count($selected) === 0) {
            // If the form name appears in the $_POST array we have a winner!
            if (isset($_POST[$name])) {
                $selected = array($_POST[$name]);
            }
        }
    
        if ($extra != '') $extra = ' '.$extra;
        $multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';
        $form = '<select name="'.$name.'"'.$extra.$multiple.">\n";
    
        foreach ($options as $key => $val) {
            $key = (string) $key;
    
            if (is_array($val) && ! empty($val)) {
                
                $form .= '<optgroup label="'.$key.'">'."\n";
    
                foreach ($val as $optgroup_key => $optgroup_val) {
                    $sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';
                    $form .= '<option value="'.$optgroup_key.'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
                }
                $form .= '</optgroup>'."\n";
            }
            else {
                $sel = (in_array($key, $selected)) ? ' selected="selected"' : '';
                $form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
            }
        }
    
        $form .= '</select>';
        return $form;
    }
     
     */
    
}



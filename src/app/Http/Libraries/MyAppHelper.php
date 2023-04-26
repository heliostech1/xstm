<?php

function myDebug($object){
    
    $isDebug = config('app.debug');
    if (!$isDebug) return;
    
    $callerName = debug_backtrace()[1]['class']."\\".debug_backtrace()[1]['function'];
    callMyDataHelperDebug("=== DEBUG BY: $callerName ==");
    callMyDataHelperDebug($object);  
}

function callMyDataHelperDebug($object) {
    return \App\Http\Libraries\DataHelper::debug($object);  
}
   

function getMyProp($data , $prop, $notFound = null){

    return \App\Http\Libraries\DataHelper::getMyProp( $data , $prop , $notFound );
}
   
function myDebugJson($object){
      $json = json_encode($object, JSON_PRETTY_PRINT);
    return \App\Http\Libraries\DataHelper::debug($json);  
}

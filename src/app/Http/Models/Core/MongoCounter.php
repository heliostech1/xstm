<?php

namespace App\Http\Models\Core;

use App\Http\Models\Core\MyBaseModel;
use DB;
use App\Http\Libraries\SqlHelper;
use App\Http\Libraries\DataHelper;
use \MongoDB\Operation\FindOneAndUpdate;

class MongoCounter extends MyBaseModel
{
    static protected $TABLE_NAME = "mg_counter";
    
    
    // https://github.com/jenssegers/laravel-mongodb/issues/734
    
    public static function getNextSequence( $collection )
    {

        /*
         $query = array( "_id" => $name);
         $update = array('$inc' => array('seq' => 1));
         $option = array("new" => true, "upsert"=> true);
        
         $db = $this->mongo_db->get_db();
         $result = $db->{$this->TABLE_NAME}->findAndModify($query, $update, null, $option);
          
         if (!empty($result) && isset($result['seq'])) {
         return $result['seq'];
         }
         */
        
        $seq = DB::getCollection(self::$TABLE_NAME)->findOneAndUpdate(
                array('_id' => $collection),
                array('$inc' => array('seq' => 1)),
                array('new' => true, 'upsert' => true, 'returnDocument' => FindOneAndUpdate::RETURN_DOCUMENT_AFTER)
                );
        
        if (!empty($seq) && isset($seq->seq)) {
            return $seq->seq;
        }
        return 0;
    }
    
}


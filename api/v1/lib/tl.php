<?php
/**
 * Created by PhpStorm.
 * User: burgalow
 * Date: 16/02/14
 * Time: 18:21
 */

class tl {





    public static function add($msg=null) {

        $prevLog=file_get_contents('logs/test.log');
        if (file_put_contents('logs/test.log',$prevLog.$msg." - ".DATESTAMP."\n")) {

        }else throw new Exception("Failed to add to test log!");

    }


}


<?php
/**
 * Created by PhpStorm.
 * User: burgalow
 * Date: 16/02/14
 * Time: 18:21
 */

class EmailLog {





    public static function add($email,$msg=null) {

        $prevLog=file_get_contents('logs/email.log');
        if (file_put_contents('logs/email.log',$prevLog.$email.'-------\n'.$msg." - ".DATESTAMP."\n\n\n")) {

        }else throw new Exception("Failed to add to email log!");

    }

} 
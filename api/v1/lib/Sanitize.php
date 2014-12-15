<?php
/**
 * Created by PhpStorm.
 * User: Edd
 * Date: 23/02/14
 * Time: 20:07
 */
class Sanitize {




    public static function normal($string) {



        return preg_replace("/[^a-zA-Z0-9\s_\-]+/", "", $string);

    }
    public static function normalNoSpace($string) {



        return preg_replace("/[^a-zA-Z0-9_\-]+/", "", $string);


    }
    public static function lettersAndNumbers($string) {



        return preg_replace("/[^a-zA-Z0-9]+/", "", $string);

    }
    public static function email($string) {
        return $string;//TODO: sanitize this string to email format

    }
    public static function password($string) {
        
        return self::lettersAndNumbers($string);
    }
}
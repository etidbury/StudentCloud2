<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 05/05/14
 * Time: 23:47
 */

class Cookie {



    public static function set($key, $value,$expires=null,$path=null)
    {
        $path=is_null($path)?CN_DEFAULT_PATH:$path;
        $_SESSION[$key] = $value;



        setcookie($key,$value,$expires,$path,null,true);
    }
    public static function delete($key) {
        unset($_COOKIE[$key]);
    }
    public static function get($key)
    {
        if (isset($_COOKIE[$key]))
            return $_COOKIE[$key];else return null;/////TODO: VALIDATE AND FILTER VALUE BEFORE RETURNING!
    }




}
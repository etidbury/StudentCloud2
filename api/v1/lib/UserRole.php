<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 23/07/14
 * Time: 17:39
 */

class UserRole {

    const USER_DEFAULT              = 1;
    const USER_STUDENT              = 2;
    const USER_TEACHER              = 6;
    const USER_ADMINISTRATOR        = 7;


    public static $roles=array(
        self::USER_DEFAULT=>"Default User",
        self::USER_STUDENT=>"Student",
        self::USER_TEACHER=>"Teacher",
        self::USER_ADMINISTRATOR=>"Administrator"
    );

    public static function exists($user_role_ID) {
        return (isset(self::$roles[$user_role_ID]));
    }
    public static function getRoleName($user_role_ID) {
        return self::exists($user_role_ID)?self::$roles[$user_role_ID]:"[Unknown Role]";
    }

}
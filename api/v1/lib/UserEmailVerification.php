<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 14/12/2014
 * Time: 18:53
 */

class UserEmailVerification {


    private $db;


    public function __construct() {



        $this->db=new DB();


    }
    public static function sendEmailVerification($user_id) {
        //todo: send email verification
        return true;
    }
}
<?php

class Authorize_Model extends Model {

    public function __construct() {
        parent::__construct();


    }

    public function postCreateAuthToken() {


        $params=array();
        $params['email']=new ParameterValidation(3,150);
        $params['password']=new ParameterValidation(3,25);


        parent::parseVars($_POST,$params);



        $auth=Authentication::init();
        $auth->loginUser($_POST['email'],$_POST['password']);


        $data=array(

            "user_id"=>Authentication::getUserID(),
            "user_role"=>Authentication::getUserRole(),
            "auth_token"=>Authentication::getAuthToken()
        );



        return r::json(1,"Authorization success",$data);



    }



    public function validateAuthToken($user_role,$user_id) {


        $valid=0;


        $auth_token=Authentication::getAuthToken();

        if (Authentication::init()->validateAuthToken($auth_token,$user_role,$user_id)) {
           $msg="Valid Token";
            $valid=1;
        }else $msg="Invalid Token: ".$auth_token;

        return r::json(1,$msg,array("validToken"=>$valid));



    }

    public function unauthorizeUser() {

        if (Authentication::logout()) {
            return r::json(SUCCESS,"Successfully un-authorized user");
        }else return r::json(0,"Failed to un-authorize user");

    }

}
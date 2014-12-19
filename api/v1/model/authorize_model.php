<?php

class Authorize_Model extends Model {

    public function __construct() {
        parent::__construct();


    }

    public function postCreateAuthToken() {


        try {


            $params = array();
            $params['email'] = new ParameterValidation(3, 150);
            $params['password'] = new ParameterValidation(3, 25);

            $post_json = file_get_contents('php://input');
            $postData = json_decode($post_json, true);


            parent::parseVars($postData, $params);

            $auth = Authentication::init();

            $auth::clearAuthSession();







            $userDetail=$auth->loginUser($postData['email'], $postData['password']);







            return r::json(AUTH_SUCCESS, "Authorization success", $userDetail);

        }catch (AuthFailure $e) {

            return r::json(0,$e->getMessage());

        }



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
            return r::json(UNAUTH_SUCCESS,"Successfully un-authorized user");
        }else return r::json(0,"Failed to un-authorize user");

    }

}
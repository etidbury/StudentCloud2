<?php

class Authorize extends Controller {

    function __construct() {
        parent::__construct();

    }

    function token($user_role=null,$user_id=null) {


        echo $this->routeCRUD(array(
            "GET"=>array("validateAuthToken",array("user_role"=>$user_role,"user_id"=>$user_id)),
            "POST"=>"postCreateAuthToken",
            "DELETE"=>"unauthorizeUser"
        ));

    }







}
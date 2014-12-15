<?php

class User extends Controller {

	function __construct() {
		parent::__construct();
	}
	
	function index() {
		$this->view->render('help/index');	
	}


    function account($user_id=null) {

        echo $this->routeCRUD(array(
            "GET"=>array("getUserAccountDetails",array("user_id"=>$user_id)),
            "POST"=>"registerUserAccount",
            "DELETE"=>array("deleteUserAccount",array("user_id"=>$user_id)),
            "PUT"=>array("updateUserAccountDetails",array("user_id"=>$user_id))


        ));
        
    }
    function account_list($from=0,$length=20)
    {
        echo $this->routeCRUD(array(
            "GET"=>array("getMultipleUserAccountDetails",array("from"=>$from,"length"=>$length))
        ));
    }



}
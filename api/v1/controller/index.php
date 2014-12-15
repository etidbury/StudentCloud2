<?php

class Index extends Controller {

	function __construct() {
		parent::__construct();

	}
	
	function index() {


        $data=array(
            "user1"=>"gareth",
            "user2"=>"antony"
        );


        echo r::json(SUCCESS,"Successfully obtained names",$data);
	}
	function boom($extra) {
        echo "boom ".$extra;
    }
    function test($id=123) {

        echo print_r($this->routeCRUD(array(
            "GET"=>"ggg",
            "UPDATE"=>"updateTestValues"

        )),true);





    }





	
}
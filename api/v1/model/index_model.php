<?php

class Index_Model extends Model {

	public function __construct() {
		parent::__construct();


	}

    public function test($id=0) {



        echo "helll";



    }
    public function ggg() {

        return r::json(1,"hello");

    }


}
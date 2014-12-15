<?php

class Error {

    function __construct() {

    }



    function renderRJSONError(CustomException $e) {


        header('Content-Type: application/json');
        echo $e->getRJSON();



    }



}
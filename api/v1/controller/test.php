<?php

class Test extends Controller {

    function __construct() {
        parent::__construct();

    }
    function login() {
        $this->view->render('test/login');
    }
    function postLogin() {

        if (!isset($_POST['email'],$_POST['pwd'])) throw new PostDataFailure();
        $auth=Authentication::init();
        if ($auth->loginUser($_POST['email'],$_POST['pwd'])) echo r::json(1,"Logged in");


    }
    function user() {


        Authentication::authorizeAccessLevel(UserRole::USER_TEACHER);

        $user_id=Authentication::getUserID();

        $user_role=Authentication::getUserRole();

        throw new LowException("user_id: {$user_id} user_role: {$user_role}");


    }






}
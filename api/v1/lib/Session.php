<?php

class Session
{
	
	public static function init()
	{
		@session_start();
	}
	
	public static function set($key, $value,$expiry)
	{
		$_SESSION[$key] = $value;

	}
    public static function secureStart() {
        $session_name = 'sec_session_id'; // Set a custom session name

        $secure = false; // Set to true if using https.

        $httponly = true; // This stops javascript being able to access the session id.



        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies.

        $cookieParams = session_get_cookie_params(); // Gets current cookies params.


        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);

        session_name($session_name); // Sets the session name to the one set above.

        if (!isset($_SESSION)) {

            @session_start();




        } // Start the php session


        session_regenerate_id(true); // regenerated the session, delete the old one.
    }
	
	public static function get($key)
	{
		if (isset($_SESSION[$key]))
		return $_SESSION[$key];else return null;
	}
	public static function delete($key) {
       if (isset($_SESSION[$key])) $_SESSION[$key]=false;

    }










	public static function destroy()
	{
		//unset($_SESSION);
		session_destroy();
	}
	
}
<?php
////

ini_set('display_errors', 'Off');
error_reporting(E_ALL||E_STRICT);
ini_set("log_errors", 1);
ini_set("error_log", "logs/exception.log");




/*
set_error_handler( 'errorHandler', E_STRICT );

function errorHandler($errno, $errstr, $errfile, $errline)
{
if (!(error_reporting() & $errno)) {
// This error code is not included in error_reporting
return true;
}

switch ($errno) {

case E_USER_ERROR:
echo "<b>My ERROR</b> [$errno] $errstr<br />\n";
echo "  Fatal error on line $errline in file $errfile";
echo ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n";
echo "Aborting...<br />\n";
exit(1);
break;

case E_USER_WARNING:
echo "<b>My WARNING</b> [$errno] $errstr<br />\n";
break;

case E_USER_NOTICE:
echo "<b>My NOTICE</b> [$errno] $errstr<br />\n";
break;

default:
echo "Unknown error type: [$errno] $errstr<br />\n";
break;
}


return true;
}*/


function fatalShutdown() {
    $le=error_get_last();

    if (!count($le)) return;
    $errmsg=implode(' - ',$le);
    error_log($errmsg);



    die('A Fatal Error has occurred.. '.$errmsg.'');



}
register_shutdown_function('fatalShutdown');



<?php

define('AUTH_TOKEN_HEADER','E-Authtoken');
///

define('DATE_TIMEZONE','Europe/London');

date_default_timezone_set(DATE_TIMEZONE);
define("FB_APP_ID","646851572031169");
define("FB_APP_SECRET","25d6988894efa282e27d8a35c9a85d69");



define('APPLICATION_NOW', time());
define("DATE_FORMAT","d/m/Y H:i:s");

define("SQL_DATE_FORMAT",'Y-m-d H:i:s');

define("DATESTAMP",date(DATE_FORMAT,APPLICATION_NOW));


define('DAY_CODE',"CCM2426".date("d"));

define("REPORT_EMAIL","info@businessapp.build");



/***********COOKIE DETAILS***************/

define("CN_DOMAIN",'/');
define("CN_DEFAULT_PATH",'/');
define("CN_USER_ID",'user_ID');
define('CN_USER_ROLE','user_role');
define("CN_AUTH_TOKEN",'auth_token');
/****************************************/
define("SUCCESS",1);
define("PROMO_SUCCESS",7741);
define("PROMO_FAILED",7742);
define("SIGNATURE_SUCCESS",7761);
define("SIGNATURE_FAILED",7762);
define("LOGIN_SUCCESS",1);
define("LOGOUT_SUCCESS",7711);
define("REGISTER_SUCCESS",7772);
define("SQL_INSERT_SUCCESS",8100);
define("SQL_UPDATE_SUCCESS",8101);
define("R_SQL_FAILED",8002);
define("AUTH_FAILED",8003);
define("AUTH_SUCCESS",8104);
define("FB_ACCOUNT_LINK_SUCCESS",8155);
define("FB_ACCOUNT_LINK_FAILED",8156);
define("RESET_PASSWORD_SUCCESS",8211);
define("RESET_PASSWORD_FAILED",8210);
define("SET_PRIMARY_EMAIL_SUCCESS",8214);
define("SET_PRIMARY_EMAIL_FAILED",8215);


define("SET_VARIABLE_SUCCESS",8216);
define("SET_VARIABLE_FAILED",8217);

define("R_PROJECT_EXISTS",8105);
define("R_NO_RESULTS",8106);
define("R_JSON_FAILED",8107);

define("R_NO_POST",8109);
define("NOTIFICATION_SUCCESS",8110);
define("NOTIFICATION_FAILURE",8111);






define("FEATURE_DEFAULT",-1);
define("FEATURE_LOW",1);
define("FEATURE_MEDIUM",2);
define("FEATURE_HIGH",3);






/*--------------------------------------------------PAYPAL-------------------------------------------------------*/
// CONFIG: Enable debug mode. This means we'll log requests into 'ipn.log' in the same directory.
// Especially useful if you encounter network errors or other intermittent problems with IPN (validation).
// Set this to 0 once you go live or don't require logging.
define("PAYPAL_DEBUG", 1);

// Set to 0 once you're ready to go live
define("PAYPAL_USE_SANDBOX", 1);




/*--------------------------------------------------/PAYPAL-------------------------------------------------------*/


/*--------------------------------------------------GOOGLE-------------------------------------------------------*/

define('GOOGLE_CLIENT_ID','167462039908-kjb8p7nk71n20rrg739qi5sjlhc1qm50.apps.googleusercontent.com');

define('GOOGLE_CLIENT_SECRET','YnINgJXutwbt5zgwHpgEDnpy');

define('GOOGLE_UNAUTHORIZE_LINK_URL','https://security.google.com/settings/security/permissions');




/*--------------------------------------------------/GOOGLE-------------------------------------------------------*/

?>
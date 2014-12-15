<?php



require 'config/errorHandling.php';
require 'config/paths.php';
require 'config/database.php';
require 'config/misc.php';

// Use an autoloader!
require 'lib/Response.php';
require 'lib/Router.php';
require 'lib/Controller.php';
require 'lib/Model.php';
require 'lib/View.php';



// Library
require 'lib/Sanitize.php';
require 'lib/tl.php';//TODO: DELETE THIS CLASS FILE AFTER TESTING
require 'lib/EmailLog.php';//TODO: DELETE THIS CLASS FILE AFTER TESTING
require 'lib/DB.php';
require 'lib/Session.php';
require 'lib/Authentication.php';//uses DB
require 'lib/Activity.php';
require 'lib/FileHandle.php';//uses DB

require 'lib/Cookie.php';
require 'lib/CustomException.php';

require 'lib/ShareStatus.php';//enum

$app = new Router();







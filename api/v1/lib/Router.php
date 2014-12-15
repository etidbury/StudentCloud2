<?php


class Router
{
    private $url;

    function __construct()
    {


        try {

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])&&$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                r::setResponseType(r::RJSON);
            }

            /*-----------------HANDLE UNDER CONSTRUCTION-------------------
                $uc_cookieID="uc123";

                if (isset($_GET[$uc_cookieID])) setcookie($uc_cookieID,1);
                if (!isset($_COOKIE[$uc_cookieID])) throw new HighException("Under Construction");
            /*-----------------/HANDLE UNDER CONSTRUCTION-------------------*/


            $url = isset($_GET['url']) ? $_GET['url'] : null;




            $url = rtrim($url, '/');





            $url = explode('/', $url);


            $this->url = $url; //scope out for other methods


            if (empty($url[0])) {

                require 'controller/index.php';
                $controller = new Index();
                $controller->loadModel('index');
                $controller->index();
                return false;


            }


            $file = 'controller/' . $url[0] . '.php';


            if (file_exists($file) && $file != "error") {

                require($file);


            }


            $className = ucfirst($url[0]);

            //if (!class_exists($className)) exit; //TODO: Add exception handler
            if (!class_exists($className)) throw new LowException("No controller found");
            $controller = new $className;


            if (method_exists($controller, 'loadModel')) {
                $controller->loadModel($url[0]);
            }else throw new LowException("loadModel method not found");
            // calling methods [1]=method name, [2]= arg 1,
            if (isset($url[2], $url[3], $url[4])) {
                if (method_exists($controller, $url[1])) {

                    $controller->{$url[1]}($url[2], $url[3], $url[4]);

                }
            } elseif (isset($url[2], $url[3])) {
                if (method_exists($controller, $url[1])) {

                    $controller->{$url[1]}($url[2], $url[3]);

                }
            } elseif (isset($url[2]) && !empty($url[2])) {
                if (method_exists($controller, $url[1])) {

                    $controller->{$url[1]}($url[2]);

                }
            } else {
                if (isset($url[1]) && !empty($url[1])) {
                    if (method_exists($controller, $url[1])) {


                        /*//////IDENTIFIES THE ARGUMENTS THAT ARE NEEDED FOR METHOD
                         * $r = new ReflectionMethod($className, $methodName);
                            $params = $r->getParameters();
                            foreach ($params as $param) {
                                //$param is an instance of ReflectionParameter
                                echo $param->getName();
                                echo $param->isOptional();
                            }
                         *
                         *
                         *
                         */

                        @$controller->{$url[1]}(); //Note: suppress warning when user does not specify argument


                    }
                } else {
                    if (method_exists($controller, 'index')) {


                        $controller->index();
                    }else throw new HighException("Failed to find action specified");



                }


            }

        } catch (LowException $le) {
            //error_log("LowException: " . $le->getMessage());


            self::routeErrorResponse($le);

            /* if (1) {//TODO: DEBUGGING
             echo "<pre>";
             print_r($le->getTrace());
             echo "</pre>";
             }*/

        } catch (HighException $he) {


            $err_msg = $he->getMessage()." stack: \n".$he->getTraceAsString();





            /* add location hint of error into log */ //TODO: REMOVE AFTER TESTING
            /*if (isset($_GET['url'])) {
                $err_msg.=" [".$_GET['url']."]";
            }else {
                $err_msg.="[NO LOCATION]";
            }*/
            /***************************************/


            error_log("HighException: " . $err_msg);

            self::routeErrorResponse($he);


        } catch (Exception $e) {
            //echo $e->getMessage();
            $msg = "A Fatal error has occurred <br/>" . $e->getMessage().":\n ".$e->getTraceAsString();


            error_log($msg);
            self::routeErrorResponse(new HighException($msg));
        }

    }


    private static function routeErrorResponse(CustomException $e)
    {

        header('Content-Type: application/json');
        echo $e->getRJSON();
        exit;
    }



}

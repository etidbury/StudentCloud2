<?php

class Controller {
    public $model;
    public $view;

	function __construct() {
		//echo 'Main controller<br />';


        $this->view = new View();



        //$this->view->setMenuListItems();
	}
	
	public function loadModel($name) {
		
		$path = 'model/'.$name.'_model.php';
		
		if (file_exists($path)) {
			require $path;
			
			$modelName = $name . '_Model';
			$this->model = new $modelName();

		}		
	}

    protected function routeCRUD($map) {

        if (array_key_exists( $_SERVER['REQUEST_METHOD'],$map)) {

            $func=$map[$_SERVER['REQUEST_METHOD']];

            $methodName=is_array($func)?$func[0]:$func;


            $optionalParams=isset($func[1])&&is_array($func[1])?$func[1]:array();




            if (!empty($methodName)&&method_exists($this->model,$methodName)) {

                $cb=call_user_func_array(array(&$this->model,$methodName),$optionalParams);
                return $cb;


            }else throw new HighException("Failed to route to ".$_SERVER['REQUEST_METHOD']." request method ({$methodName}) in object ".print_r($this->model,true));

        }else throw new HighException("Unspecified Request Handler");
        return null;
    }



}
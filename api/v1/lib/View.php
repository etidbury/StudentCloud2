<?php

class View {


	function __construct() {
		//echo 'this is the view';

	}
    public function render($name, $cssNames=array(),$jsNames=array(),$noInclude = false)
    {

        header('Content-Type: text/html');

        $slashPos=strpos($name,"/");
        $ctlr=substr($name,0,$slashPos);

        $page=substr($name,$slashPos+1,strlen($name));

        /*----------- CSS files ------------*/
        $cssNames[]=$page;
        foreach($cssNames as $cssName) {

            $dir="view/".$ctlr."/css/".$cssName.".css";


            //todo:check file exists
            $this->css[]=$dir;




        }
        /*----------- --------- ------------*/
        /*----------- CSS files ------------*/
        $jsNames[]=$page;
        foreach($jsNames as $jsName) {

            $dir="view/".$ctlr."/js/".$jsName.".js";


            //todo:check file exists
            $this->js[]=$dir;




        }
        /*----------- --------- ------------*/


        if ($noInclude == true) {
            require 'view/' . $name . '.php';
        }
        else {
            require 'view/header.php';



            if (!include('view/' . $name . '.php')) {

                echo "404 errorz";
            }

            require 'view/footer.php';
        }
    }










}
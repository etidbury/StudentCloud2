<?php
require('lib/ParameterValidation.php');
class Model {
    public $response="";
	function __construct() {
        $this->db=new DB();//database access on every model

        $this->auth=new Authentication();//authentication on every model

	}
    protected static function parseVars($array,$params) {



        

        $validParams=array();

        $notSetParams=array();
        $invalidParams=array();

        foreach ($params as $requiredParam=>$ParameterValidation) {

            if (!isset($array[$requiredParam])) {
                $notSetParams[]=$requiredParam;
                continue;
            }

            if (!is_object($ParameterValidation)) throw new HighException("Invalid Parameter Validation specified");


            if ($ParameterValidation->isValidParam($array[$requiredParam]))
                $validParams[$requiredParam]=$array[$requiredParam];
            else $invalidParams[]=$requiredParam;

        }
        if (count($notSetParams)>0) throw new LowException("Required parameters were not set: ".implode(',',$notSetParams));
        if (count($invalidParams)>0) throw new LowException("Required parameters were invalid: ".implode(',',$invalidParams));

    }




}
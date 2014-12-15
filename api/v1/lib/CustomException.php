<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 13/08/14
 * Time: 17:22
 */
require 'lib/HighException.php';
require 'lib/LowException.php';
class CustomException extends Exception {
    public function getRJSON() {

        $r=array();

        $r['r']=array();

        $r['r']['code']=$this->getCode();

        $r['r']['message']=$this->getMessage();//todo: amend after testing


        $r['r']['stack']=$this->getTraceAsString();


        return json_encode($r);




    }
} 
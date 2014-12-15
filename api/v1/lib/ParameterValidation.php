<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 11/12/2014
 * Time: 09:25
 */

class ParameterValidation {


    private $min_length=0;
    private $max_length=500;

    public function __construct($min_length,$max_length) {
        $this->setMinMaxLength($min_length,$max_length);
    }
    public function getMinLength() {
        return $this->min_length;
    }
    public function getMaxLength() {
        return $this->max_length;
    }
    public function setMinMaxLength($min_length,$max_length) {
        if (is_int($min_length)&&is_int($min_length)&&$max_length>$min_length) {
            $this->min_length=$min_length;
            $this->max_length=$max_length;
        }
    }

    public function isValidParam($value) {
        $len=strlen($value);
       return ($len>=$this->getMinLength()&&$len<=$this->getMaxLength());
    }



} 
<?php
namespace App\modules;

abstract class Module {
    private $format = null;

    public function __construct()
    {
        
    }

    public function getData(){
        
    }

    abstract public function generateData();

}
<?php
namespace App\services\data;

use App\formats\Format;

class BlockModuleData {
    private $name = null;
    private $args = [];
    private $data = null;

    public function __construct(string $name, array $args)
    {
        $this->name = $name;
        $this->args = $args;
    }

    public function getName(){
        return $this->name;
    }

    public function getArgs(){
        return $this->args;
    }

    public function setData(Format $data){
        $this->data = $data;
    }

    public function getData(){
        return $this->data;
    }
    
}
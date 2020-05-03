<?php
namespace App\content;

abstract class Content {
   
    public function __construct($args)
    {
        $this->loadData($args);
    }

    protected abstract function loadData();

    public function getType(){
        $name = explode('\\',get_class($this));
        $name = $name[count($name)-1];
        $name = substr($name, 0, -count('Content'));

        return $name;
    }

    public static function create(string $type, array $args){
        $type = 'App\\content\\'.$type.'Content';
        if(class_exists($type)){
            return new $type($args);
        }
        return null;
    }
}
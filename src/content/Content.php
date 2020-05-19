<?php
namespace App\content;

abstract class Content {
    private $loaded = false;
   
    public function __construct($args)
    {
        $this->loaded = $this->loadData($args);
    }

    protected abstract function loadData(array $args);

    public function getType(){
        $name = explode('\\',get_class($this));
        $name = $name[count($name)-1];
        $name = substr($name, 0, -strlen('Content'));

        return strtolower($name);
    }

    public static function create(string $type, array $args){
        $type = 'App\\content\\'.ucfirst($type).'Content';
        if(class_exists($type)){
            return new $type($args);
        }
        return null;
    }

    public function isLoaded(){
        return $this->loaded;
    }
}
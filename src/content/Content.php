<?php
namespace App\content;

abstract class Content {
    private $loaded = false;
   
    public function __construct(array $args, array $managers, array $get = []){
        $this->loaded = $this->loadData($args, $managers, $get);
    }

    protected abstract function loadData(array $args, array $managers, array $get = []);

    public function getType(){
        $name = explode('\\',get_class($this));
        $name = $name[count($name)-1];
        $name = substr($name, 0, -strlen('Content'));

        return strtolower($name);
    }

    public static function create(string $type, array $args, array $managers, array $get = []){
        $type = 'App\\content\\'.ucfirst($type).'Content';
        if(class_exists($type)){
            return new $type($args, $managers, $get);
        }
        return null;
    }

    public function isLoaded(){
        return $this->loaded;
    }
}
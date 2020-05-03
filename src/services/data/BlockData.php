<?php
namespace App\services\data;

class BlockData {
    private $name = '';
    private $active = false;
    private $modules = [];

    public function __construct(string $name, bool $active = true)
    {
        $this->name = $name;
        $this->active = $active;
    }

    public function addModule(BlockModuleData $module){
        $this->modules[] = $module;
    }

    public function getModules(){
        return $this->modules;
    }

    public function getName(){
        return $this->name;
    }

    public function isActive(){
        return $this->active;
    }

}
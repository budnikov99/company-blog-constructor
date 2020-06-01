<?php
namespace App\Plugins;

class ModuleArgument {
    public static $TYPE_STRING = 'text';
    public static $TYPE_LIST = 'list';
    public static $TYPE_NUMBER = 'number';
    public static $TYPE_BOOLEAN = 'boolean';
    public static $TYPE_FILE = 'file';

    private $module = null;

    private $name = '';
    private $title = '';
    private $type = '';
    private $validator = null;
    private $valuelist = null;

    public static function isTypeValid(string $type){
        return in_array($type, array(ModuleArgument::$TYPE_LIST, 
            ModuleArgument::$TYPE_STRING, 
            ModuleArgument::$TYPE_NUMBER, 
            ModuleArgument::$TYPE_BOOLEAN,
            ModuleArgument::$TYPE_FILE));
    }

    public function __construct(Module $module, $name, $type, $title){
        if(!ModuleArgument::isTypeValid($type)){
            throw new ModuleException('Аргумент '.$name.' модуля '.get_class($module).
            ' имеет неизвестный тип '.$type);                   
        }

        $this->module = $module;
        $this->name = $name;
        $this->title = $title;
        $this->type = $type;

        if(method_exists($module, $name.'Validate')){
            $this->validator = $name.'Validate';
        }

        if(method_exists($module, $name.'Values')){
            $this->valuelist = $name.'Values';
        }else if($type == ModuleArgument::$TYPE_LIST){
            throw new ModuleException('Аргумент '.$name.' модуля '.get_class($module).
                    ' имеет тип list, но не содержит метода '.$name.'Values()');
        }
    }

    public function hasValidator(){
        return !is_null($this->validator);
    }

    public function validate($value){
        if($this->validator !== null){
            return $this->module->$this->validator($value);
        }else{
            return true;
        }
    }

    public function hasValuelist(){
        return !is_null($this->valuelist);
    }

    public function values(){
        if($this->valuelist !== null){
            return $this->module->{$this->valuelist}();
        }
        return null;
    }

    public function getName(){
        return $this->name;
    }

    public function getType(){
        return $this->type;
    }

    public function getTitle(){
        return $this->title;
    }
}
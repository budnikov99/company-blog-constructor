<?php
namespace App\modules;

class ModuleArgument {
    public static $TYPE_STRING = 'string';
    public static $TYPE_LIST = 'list';
    public static $TYPE_NUMBER = 'number';
    public static $TYPE_BOOLEAN = 'boolean';

    public $module = null;

    public $name = '';
    public $title = '';
    public $type = '';
    public $validator = null;
    public $valuelist = null;
    public $required = false;

    public function __construct(Module $module, $name, $type, $title, $required){
        if(!in_array($type, array(ModuleArgument::$TYPE_LIST, 
                            ModuleArgument::$TYPE_STRING, 
                            ModuleArgument::$TYPE_NUMBER, 
                            ModuleArgument::$TYPE_BOOLEAN))){
            throw new ModuleException('Аргумент '.$name.' модуля '.get_class($module).
            ' имеет неизвестный тип '.$type);                   
        }

        $this->module = $module;
        $this->name = $name;
        $this->title = $title ?: $module;
        $this->type = $type;
        $this->required = $required;

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

    public function validate($value){
        if($this->validator != null){
            return $this->module->$this->validator($value);
        }else{
            if($this->type == ModuleArgument::$TYPE_LIST){
                return in_array($value, $this->module->$this->valuelist());
            }else{
                return true;
            }
        }
    }

    public function values(){
        if($this->valuelist != null){
            return $this->module->$this->valuelist();
        }
        return null;
    }

}
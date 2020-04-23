<?php
namespace App\modules;

use App\formats\FormatException;
use App\formats\Format;

abstract class Module {
    
    private $format = '';
    private $title = '';
    private $arglist = array();

    public function __construct($title, $format, $arglist)
    {
        $this->format = $format;
        $this->title = $title;
        foreach($arglist as $name => $data){
            $this->arglist[$name] = new ModuleArgument($this, $name, $data['type'], $data['title'], $data['required']);    
        }
    }

    public function getFormat(){
        return $this->format;
    }

    public function getData($args){
        $data = $this->generateData($args);
        if(!is_subclass_of($data, Format::class)){
            throw new FormatException('Модуль вернул неожиданное значение '.gettype($data));
        }

        if($data->getFormatName() != $this->format){
            throw new FormatException('Ожидаются данные в формате '.$this->format.', получен '.$data->getFormatName());
        }

        return $data->getData();
    }

    public function getArgument($name){
        if(array_key_exists($name, $this->arglist)){
            return $this->arglist[$name];
        }
        return null;
    }

    public function getTitle(){
        return $this->title;
    }

    abstract protected function generateData($args);

}
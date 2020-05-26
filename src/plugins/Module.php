<?php
namespace App\plugins;

use App\formats\FormatException;
use App\formats\Format;

abstract class Module {
    private $name = '';
    private $format = '';
    private $title = '';
    private $arglist = array();

    public function __construct(string $name, string $title, string $format, array $arglist)
    {
        $this->name = $name;
        $this->format = $format;
        $this->title = $title;
        foreach($arglist as $name => $data){
            $this->arglist[$name] = new ModuleArgument($this, $name, $data['type'], $data['title']);    
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

    public function getName(){
        return $this->name;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getArgumentList(){
        return $this->arglist;
    }

    abstract protected function generateData($args);

}
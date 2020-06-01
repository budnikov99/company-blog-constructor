<?php
namespace App\Services\Data;

use App\Plugins\Module;

class ModuleData extends Data {
    private $name = '';
    private $format = '';
    private $title = '';
    private $arglist = [];

    public function __construct(string $name, string $title, string $format){
        $this->name = $name;
        $this->title = $title;
        $this->format = $format;
    }

    public static function createFromModule(Module $module){
        $data = new ModuleData($module->getName(), $module->getTitle(), $module->getFormat());

        foreach($module->getArgumentList() as $key => $value){
            $data->addArgument(ModuleArgumentData::createFromModuleArgument($value));
        }

        return $data;
    }

    public function addArgument(ModuleArgumentData $arg){
        $this->arglist[$arg->getName()] = $arg;
    }

    public function getArguments(){
        return $this->arglist;
    }

    public function getName(){
        return $this->name;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getFormat(){
        return $this->format;
    }

    protected static function deserialize_raw(array $data){
        Data::assertValueType($data['name'], 'string');
        Data::assertValueType($data['title'], 'string');
        Data::assertValueType($data['format'], 'string');

        $md = new ModuleData($data['name'], $data['title'], $data['format']);

        if(is_array($data['arguments'])){
            foreach($data['arguments'] as $key => $value){
                $arg = ModuleArgumentData::deserialize($value);
                if(is_null($arg)){
                    return null;
                }else{
                    $md->addArgument($arg);
                }
            }
        }

        return $md;
    }

    public function serialize(){
        $data = [
            'name' => $this->name,
            'title' => $this->title,
            'format' => $this->format,
            'arguments' => [],
        ];

        foreach($this->arglist as $key => $value){
            $data['arguments'][$key] = $value->serialize();
        }

        return $data;
    }

}

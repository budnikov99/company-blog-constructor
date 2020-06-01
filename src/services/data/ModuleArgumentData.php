<?php
namespace App\Services\Data;

use App\Plugins\ModuleArgument;

class ModuleArgumentData extends Data {
    private $name = '';
    private $title = 'Аргумент';
    private $type = '';
    private $values = [];

    public function __construct(string $name, string $title, string $type)
    {
        if(!ModuleArgument::isTypeValid($type)){
            $type = ModuleArgument::$TYPE_STRING;
        }
        $this->name = $name;
        $this->title = $title;
        $this->type = $type;
    }

    public static function createFromModuleArgument(ModuleArgument $arg){
        $data = new ModuleArgumentData($arg->getName(), $arg->getTitle(), $arg->getType());
        if($arg->hasValueList()){
            $data->setValues($arg->values());
        }
        return $data;
    }

    public function getName(){
        return $this->name;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getType(){
        return $this->type;
    }

    public function getValues(){
        return $this->values;
    }

    public function setValues(array $values){
        $this->values = $values;
    }

    protected static function deserialize_raw(array $data){
        Data::assertValueType($data['name'], 'string');
        Data::assertValueType($data['title'], 'string');
        Data::assertValueType($data['type'], 'string');

        $arg = new ModuleArgumentData($data['name'], $data['title'], $data['type']);
        return $arg;
    }

    public function serialize(){
        $data = [
            'name' => $this->name,
            'title' => $this->title,
            'type' => $this->type,
            'list' => $this->values,
        ];

        return $data;
    }

}

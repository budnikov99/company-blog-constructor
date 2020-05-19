<?php
namespace App\services\data;

use App\formats\Format;

class BlockModuleData extends Data {
    private $name = null;
    private $args = [];
    private $data = null;

    public function __construct(string $name, array $args)
    {
        $this->name = $name;
        $this->args = $args;
    }

    public function getName(){
        return $this->name;
    }

    public function getArgs(){
        return $this->args;
    }

    public function setData(Format $data){
        $this->data = $data;
    }

    public function getData(){
        return $this->data;
    }

    protected static function deserialize_raw(array $data){
        Data::assertValueType($data['module'], 'string');
        return new BlockModuleData($data['module']??null, $data);
    }

    public function serialize(){
        $data = $this->args;
        $data['module'] = $this->name;
        return $data;
    }
}
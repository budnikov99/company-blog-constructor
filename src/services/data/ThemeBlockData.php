<?php
namespace App\Services\Data;

class ThemeBlockData extends Data {
    private $title = 'Блок без названия';
    private $multiple = false;
    private $accepted_formats = [];

    public function __construct($title)
    {
        $this->title = $title; 
    }

    public function getTitle(){
        return $this->title;
    }

    public function isMultiple(){
        return $this->multiple;
    }

    public function setMultiple(bool $multiple){
        $this->multiple = $multiple;
    }

    public function getAcceptedFormats(){
        return $this->accepted_formats;
    }

    public function setAcceptedFormats(array $formats){
        $this->accepted_formats = $formats;
    }

    protected static function deserialize_raw(array $data){
        Data::assertValueType($data['title'], 'string');
        Data::assertValueType($data['multiple'], 'boolean');

        $block = new ThemeBlockData($data['title']);

        $block->setMultiple($data['multiple']);
        $formats = $data['accepted_formats']??[];
        if(!is_array($formats)){
            $formats = [];
        }
        $block->setAcceptedFormats($formats);
        return $block;
    }

    public function serialize(){
        $data = [
            'title' => $this->title,
            'multiple' => $this->multiple,
            'accepted_formats' => $this->accepted_formats??[]
        ];

        return $data;
    }

}
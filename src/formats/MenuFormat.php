<?php
namespace App\formats;

use App\formats\Format;

class MenuFormat extends Format {
    private $items = array();

    public function addItem($text, $url){
        $this->items[] = array('text' => $text, 'url' => $url);
        return count($this->items)-1;
    }

    public function addChild($index, $text, $url){
        if($index < 0 || $index >= count($this->items)){
            return false;
        }

        if(!isset($this->items[$index]['children'])){
            $this->items[$index]['children'] = array();
        }

        $this->items[$index]['children'][] = array('text' => $text, 'url' => $url);
        return true;
    }

    protected function toData()
    {
        return $this->items;
    }
}
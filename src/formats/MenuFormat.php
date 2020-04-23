<?php
namespace App\formats;

use App\formats\Format;

class MenuFormat extends Format {
    public $items = array();

    public function addItem($url, $text){
        $this->items[] = array('text' => $text, 'url' => $url);
        return count($this->items)-1;
    }

    public function addChild($index, $url, $text){
        if($index < 0 || $index >= count($this->items)){
            return false;
        }

        $this->items[$index]['children'][] = array('text' => $text, 'url' => $url);
        return true;
    }

}
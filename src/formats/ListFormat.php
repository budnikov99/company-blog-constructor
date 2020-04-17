<?php
namespace App\formats;

use App\formats\Format;

class ListFormat extends Format {
    public $items = array();

    public function addItem($item){
        $this->items[] = $item;
    }


}
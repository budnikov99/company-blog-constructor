<?php
namespace App\formats\lists;

use App\formats\Format;
use App\formats\TextFormat;

class TextListFormat extends Format {
    public $items = array();

    public function addItem(TextFormat $item){
        $this->items[] = $item;
    }
}
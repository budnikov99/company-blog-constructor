<?php
namespace App\formats\lists;

use App\formats\Format;
use App\formats\ButtonFormat;

class ButtonListFormat extends Format {
    public $items = array();

    public function addItem(ButtonFormat $item){
        $this->items[] = $item;
    }
}
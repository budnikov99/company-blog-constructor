<?php
namespace App\formats;

use App\formats\Format;

class ListFormat extends Format {
    private $items = array();

    public function addItem($item){
        $this->items[] = $item;
    }

    protected function toData()
    {
        return $this->items;
    }
}
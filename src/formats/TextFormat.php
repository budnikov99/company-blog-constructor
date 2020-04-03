<?php
namespace App\formats;

use App\formats\Format;

class TextFormat extends Format {
    private $text = '';

    public function setText($text){
        $this->text = $text;
    }

    protected function toData()
    {
        return $this->text;
    }
}
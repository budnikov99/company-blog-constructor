<?php
namespace App\formats;

use App\formats\Format;

class TextFormat extends Format {
    public $text = '';

    public function setText($text){
        $this->text = $text;
    }

}
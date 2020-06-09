<?php
namespace App\formats;

use App\formats\Format;

class LinkFormat extends Format {
    public $href = '';
    public $text = '';

    public function setHref($href){
        $this->href = $href;
    }

    public function setText($text){
        $this->text = $text;
    }
}
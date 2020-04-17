<?php
namespace App\formats;

use App\formats\Format;

class ButtonFormat extends Format {
    public $url = '';
    public $text = '';

    public function setUrl($url){
        $this->url = $url;
    }

    public function setText($text){
        $this->text = $text;
    }

}
<?php
namespace App\formats;

use App\formats\Format;

class LinkFormat extends Format {
    public $url = '';
    public $text = '';

    public function setUrl($url){
        $this->url = $url;
    }

    public function setText($text){
        $this->text = $text;
    }

}
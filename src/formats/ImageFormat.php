<?php
namespace App\formats;

use App\formats\Format;

class ImageFormat extends Format {
    public $url = '';
    public $alt = '';

    public function setUrl($url){
        $this->url = $url;
    }

    public function setAlt($alt){
        $this->title = $alt;
    }

}
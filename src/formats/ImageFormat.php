<?php
namespace App\formats;

use App\formats\Format;

class ImageFormat extends Format {
    public $url = '';
    public $title = '';

    public function setUrl($url){
        $this->url = $url;
    }

    public function setTitle($title){
        $this->title = $title;
    }

}
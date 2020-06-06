<?php
namespace App\formats;

use App\formats\Format;

class ImageLinkFormat extends Format {
    public $href = '';
    public $text = '';
    public $img = null;

    public function setHref($href){
        $this->href = $href;
    }

    public function setText($text){
        $this->text = $text;
    }

    public function setImg(string $img){
        $this->img = $img;
    }

}
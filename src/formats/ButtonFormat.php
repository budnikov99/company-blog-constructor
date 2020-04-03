<?php
namespace App\formats;

use App\formats\Format;

class ButtonFormat extends Format {
    private $url = '';
    private $text = '';

    public function setUrl($url){
        $this->url = $url;
    }

    public function setText($text){
        $this->text = $text;
    }

    protected function toData()
    {
        return array('url' => $this->url, 'text' => $this->text);
    }
}
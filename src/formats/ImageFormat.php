<?php
namespace App\formats;

use App\formats\Format;

class ImageFormat extends Format {
    private $url = '';
    private $title = '';

    public function setUrl($url){
        $this->url = $url;
    }

    public function setTitle($title){
        $this->title = $title;
    }

    protected function toData()
    {
        return array('url' => $this->url, 'title' => $this->title);
    }
}
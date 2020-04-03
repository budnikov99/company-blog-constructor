<?php
namespace App\formats;

use App\formats\Format;

class HTMLFormat extends Format {
    private $html = '';

    public function setHTML($html){
        $this->html = $html;
    }

    protected function toData()
    {
        return $this->html;
    }
}
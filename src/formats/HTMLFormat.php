<?php
namespace App\formats;

use App\formats\Format;

class HTMLFormat extends Format {
    public $html = '';

    public function setHTML($html){
        $this->html = $html;
    }

}
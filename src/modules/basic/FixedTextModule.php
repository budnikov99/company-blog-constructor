<?php
namespace App\modules\basic;

use App\formats\TextFormat;
use App\modules\Module;

class FixedTextModule extends Module {
    protected function generateData($args){
        $res = new TextFormat();
        $res->setText($args['text']);
        return $res;
    }
}
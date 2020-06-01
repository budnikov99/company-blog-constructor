<?php
namespace Plugins\_basic\modules;

use App\formats\TextFormat;
use App\Plugins\Module;

class FixedTextModule extends Module {
    protected function generateData($args){
        $res = new TextFormat();
        $res->setText($args['text']);
        return $res;
    }
}
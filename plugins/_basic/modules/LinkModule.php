<?php
namespace Plugins\_basic\modules;

use App\formats\LinkFormat;
use App\Plugins\Module;

class LinkModule extends Module {
    protected function generateData($args){
        $res = new LinkFormat();
        $res->setText($args['text'] ?? null);
        $res->setHref($args['href']);
        return $res;
    }
}
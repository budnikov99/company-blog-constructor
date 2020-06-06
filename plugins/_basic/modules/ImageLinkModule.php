<?php
namespace Plugins\_basic\modules;

use App\formats\ImageLinkFormat;
use App\Plugins\Module;

class ImageLinkModule extends Module {
    protected function generateData($args){
        $res = new ImageLinkFormat();
        $res->setImg($args['img']);
        $res->setText($args['text'] ?? null);
        $res->setHref($args['href']);
        return $res;
    }
}
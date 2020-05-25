<?php
namespace Plugins\_basic\modules;

use App\formats\ImageFormat;
use App\plugins\Module;

class ImageURLModule extends Module {
    protected function generateData($args){
        $res = new ImageFormat();
        $res->setUrl($args['url']);
        $res->setAlt($args['alt'] ?? null);
        return $res;
    }
}
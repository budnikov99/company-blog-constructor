<?php
namespace App\modules\basic;

use App\formats\ImageFormat;
use App\modules\Module;

class ImageURLModule extends Module {
    protected function generateData($args){
        $res = new ImageFormat();
        $res->setUrl($args['url']);
        $res->setAlt($args['alt'] ?? null);
        return $res;
    }
}
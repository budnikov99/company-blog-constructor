<?php
namespace App\modules\basic;

use App\formats\MenuFormat;
use App\modules\Module;

class MenuModule extends Module {
    protected function generateData($args){
        return new MenuFormat();
    }

    public function menuValues(){
        return array('test1','test2');
    }

}

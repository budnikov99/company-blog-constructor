<?php
namespace App\modules\basic;

use App\formats\MenuFormat;
use App\modules\Module;

class MenuModule extends Module {
    protected function generateData($args){
        $result = new MenuFormat();
        $result->addItem(null, 'Два');
        $result->addItem('/theme/css/base.scss', 'Стула (base.scss)');
        $result->addItem('#', 'Пики #');
        $result->addItem(null, 'Точёные');
        $result->addItem(null, 'Море');
        $result->addItem(null, 'Лес');
        $result->addItem(null, 'Полянка');

        $result->addChild(0, null, 'НепикИ');
        $result->addChild(0, null, 'Неточёные');
        $result->addChild(0, null, 'пикИ');
        $result->addChild(0, null, 'точёные');

        $result->addChild(3, null, 'Члены');
        $result->addChild(3, null, 'Дрочёные');
        $result->addChild(3, null, 'неЧлены');
        $result->addChild(3, null, 'неДрочёные');

        return $result;
    }

    public function menuValues(){
        return array('test1','test2');
    }

}

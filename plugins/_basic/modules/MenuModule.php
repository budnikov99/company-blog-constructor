<?php
namespace Plugins\_basic\modules;

use App\formats\MenuFormat;
use App\Plugins\Module;

class MenuModule extends Module {
    protected function generateData($args){
        $result = new MenuFormat();
        if($args['menu'] == 'menu1'){
            $result->addItem(null, 'Меню1');
            $result->addItem(null, 'Меню2');
            $result->addItem(null, 'Меню3');
            $result->addItem(null, 'Меню4');
            $result->addItem(null, 'Меню5');
            $result->addItem(null, 'Меню6');

            $result->addChild(0, null, 'тест1');
            $result->addChild(0, null, 'тест2');
            $result->addChild(0, null, 'тест3');
            $result->addChild(0, null, 'тест4');

        } else if($args['menu'] == 'menu2'){
            $result->addItem(null, 'Тест1');
            $result->addItem(null, 'Тест2');
            $result->addItem(null, 'Тест3');
            $result->addItem(null, 'Тест4');
            $result->addItem(null, 'Тест5');

        } else if($args['menu'] == 'pages_menu'){
            $result->addItem('/', 'Главная');
            $result->addItem('/news', 'Новости');
            $result->addItem('/about', 'О нас');
            $result->addItem('/contacts', 'Контакты');
        }

        return $result;
    }

    public function menuValues(){
        return array('pages_menu','menu1','menu2');
    }

}

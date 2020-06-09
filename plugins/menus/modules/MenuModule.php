<?php
namespace Plugins\menus\modules;

use App\formats\MenuFormat;
use App\Plugins\ArgListItem;
use App\Plugins\Module;
use Plugins\menus\MenuLoader;

class MenuModule extends Module {
    protected function generateData($args){
        $ml = MenuLoader::get();
        $menu = $ml->getMenu($args['menu']??'');
        if($menu){
            return $menu->toMenuFormat();
        }else{
            return new MenuFormat();
        }
    }

    public function menuValues(){
        $list = [];
        foreach(MenuLoader::get()->getMenus() as $name => $menu){
            $list []= new ArgListItem($menu->getTitle(), $name);
        }

        return $list;
    }

}

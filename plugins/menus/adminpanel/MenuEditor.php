<?php
namespace Plugins\menus\adminpanel;

use App\Plugins\AdminPanelExtension;
use App\Services\PageManager;
use Plugins\menus\MenuData;
use Plugins\menus\MenuLoader;
use Symfony\Component\HttpFoundation\Request;

class MenuEditor extends AdminPanelExtension {
    public function getSubmenu(string $subpath, Request $request){
        $ml = MenuLoader::get();
        $menus = ['' => 'Создать'];
        foreach($ml->getMenus() as $name => $menu){
            $menus[$name] = $menu->getTitle();
        }
        $data = [
            'active' => '',
            'items' => $menus,
        ];
        return $data;
    }

    public function getTemplateData(string $subpath, Request $request){
        $ml = MenuLoader::get();

        $name = $subpath;
        $menu = $ml->getMenu($name);

        if(is_null($menu)){
            $subpath = '';
            $menu = new MenuData('');
        }

        $menu_ids = [];
        foreach($ml->getMenus() as $mname => $_){
            $menu_ids []= $mname;
        }

        $pages = [];
        foreach($this->managers[PageManager::class]->getPageList() as $page){
            if($page[0] != '_'){
                $pages []= $page;
            }
        }
    
        return [ 
            'active' => $subpath,
            'template' => 'menus/templates/menu-editor.html.twig',
            'data' => [
                'menu_name' => $name,
                'mode' => ($subpath=='')?'create':'edit',
                'menu_name' => $subpath,
                'menu' => $menu,
                'menu_json' => json_encode($menu->serialize()),
                'existing_menus_json' => json_encode($menu_ids),
                'page_list_json' => json_encode($pages),
            ],
        ];
    }
}
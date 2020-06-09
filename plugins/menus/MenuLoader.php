<?php
namespace Plugins\menus;

use Pluguns\menus\MenuData;
use Symfony\Component\Yaml\Yaml;

class MenuLoader {
    private $menus = [];
    private static $instance = null;

    public static function get(){
        if(is_null(MenuLoader::$instance)){
            MenuLoader::$instance = new MenuLoader();
        }
        return MenuLoader::$instance;
    }

    private function __construct(){
        if(!file_exists(SERVER_ROOT.'/data/menus.yaml')){
            file_put_contents(SERVER_ROOT.'/data/menus.yaml', '');
        }else{
            foreach(Yaml::parseFile(SERVER_ROOT.'/data/menus.yaml')??[] as $name => $data){
                $this->menus[$name] = MenuData::deserialize($data);
            }
        }
    }

    public function getMenu(string $name){
        if(array_key_exists($name, $this->menus)){
            return $this->menus[$name];
        }
        return null;
    }

    public function getMenus(){
        return $this->menus;
    }

    public function setMenu(string $name, MenuData $menu){
        $this->menus[$name] = $menu;
    }

    public function removeMenu($name){
        if(array_key_exists($name, $this->menus)){
            $menu = $this->menus[$name];
            unset($this->menus[$name]);
            return $menu;
        }
        return null;
    }

    public function saveMenus(){
        $data = [];
        foreach($this->menus as $name => $menu){
            $data[$name] = $menu->serialize();
        }

        file_put_contents(SERVER_ROOT.'/data/menus.yaml', Yaml::dump($data));
    }
}
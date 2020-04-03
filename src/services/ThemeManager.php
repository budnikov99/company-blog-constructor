<?php
namespace App\services;

use Symfony\Component\Yaml\Yaml;

class ThemeManager {
    private $active_theme = null;
    private $theme_dir = null;
    private $page_settings = null;

    public function __construct()
    {
        if(!is_null($this->active_theme)){
            return false;
        }
        $theme_settings = Yaml::parseFile(SERVER_ROOT.'\\themes\\theme.yaml');
        $this->active_theme = $theme_settings['active-theme'];
        $this->theme_dir = SERVER_ROOT.'\\themes\\'.$this->active_theme.'\\';

        $this->page_settings = Yaml::parseFile($this->theme_dir.'settings.yaml');

        return true;
    }

    private function loadFile($path){
        if(file_exists($path)){
            return file_get_contents($path);
        }else{
            return null;
        }
    }

    public function getAssetFile($filename){
        return $this->loadFile($this->theme_dir.$filename);
    }

    public function getSettings(){
        return $this->page_settings;
    }

}
<?php
namespace App\Services;

use App\Services\Data\ThemeBlockData;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\Server\Server;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Yaml\Yaml;

class ThemeManager extends Manager {
    private $active_theme = null;
    private $theme_dir = null;
    private $theme_data = null;
    private $settings = [];
    private $blocks = [];
    private $themes = [];

    public function __construct() {
        if(!is_null($this->active_theme)){
            return false;
        }

        $theme_dir_list = scandir(SERVER_ROOT.'/themes');
        foreach($theme_dir_list as $theme){
            if(in_array($theme, ['.', '..'])){
                continue;
            }
            $dir = SERVER_ROOT.'/themes/'.$theme;
            if(is_dir($dir) && file_exists($dir.'/theme.yaml')){
                $data = Yaml::parseFile($dir.'/theme.yaml');
                if($data){
                    $this->themes[$theme] = $data['title'];
                }
            }
        } 

        $theme_settings = Yaml::parseFile(SERVER_ROOT.'/themes/theme.yaml');
        $this->active_theme = $theme_settings['active-theme'];
        $this->theme_dir = SERVER_ROOT.'/themes/'.$this->active_theme;

        $this->theme_data = Yaml::parseFile($this->theme_dir.'/theme.yaml');
        if(file_exists($this->theme_dir.'/settings.yaml')){
            $this->settings = Yaml::parseFile($this->theme_dir.'/settings.yaml');
        }

        foreach($this->theme_data['blocks'] as $block_name => $block_data){
            $this->blocks[$block_name] = ThemeBlockData::deserialize($block_data);
        }

        return true;
    }


    public function getThemeData(){
        return $this->theme_data;
    }

    public function getBlocks(){
        return $this->blocks;
    }

    public function getSettings(){
        return $this->settings;
    }

    public function setSettingValue(string $setting, $value){
        if(array_key_exists($setting, $this->settings)){
            $this->settings[$setting]['value'] = $value;
            return true;
        }
        return false;
    }

    public function saveSettings(){
        if(empty($this->settings)){
            return false;
        }
        file_put_contents($this->theme_dir.'/settings.yaml', Yaml::dump($this->getSettings()));
        return true;
    }

    public function getMainTemplate(){
        return $this->theme_data['main_template'];
    }

    public function getThemeDir(){
        return $this->theme_dir;
    }

    public function getActiveTheme(){
        return $this->active_theme;
    }

    public function setActiveTheme(string $name){
        if(!array_key_exists($name, $this->themes)){
            return false;
        }
        $data = Yaml::parseFile(SERVER_ROOT.'/themes/theme.yaml');
        $data['active-theme'] = $name;
        file_put_contents(SERVER_ROOT.'/themes/theme.yaml', Yaml::dump($data));
        return true;
    }

    public function getThemesList(){
        return $this->themes;
    }

    public function getThemeDataByName(string $theme){
        if($theme == $this->active_theme){
            return $this->getThemeData();
        }else if(file_exists(SERVER_ROOT.'/themes/'.$theme.'/theme.yaml')){
            return Yaml::parseFile(SERVER_ROOT.'/themes/'.$theme.'/theme.yaml');
        }else{
            return null;
        }
    }
}
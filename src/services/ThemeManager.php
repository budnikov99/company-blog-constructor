<?php
namespace App\services;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\Server\Server;
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
        return $this->loadFile($this->theme_dir.'assets\\'.$filename);
    }

    public function getStyle($filename){
        $dir = $this->theme_dir.'assets\\css\\';
        
        if(!file_exists($dir.$filename)){
            return null;
        }

        if(substr($filename, -5) == '.scss'){
            $cache_filename = $filename.'.cache';

            if(!file_exists($dir.$cache_filename) || filemtime($dir.$filename) > filemtime($dir.$cache_filename)){
                $scss = new Compiler();
                $scss->setFormatter('ScssPhp\ScssPhp\Formatter\Compressed');
                $scss->setImportPaths($dir);

                $file = $scss->compile($this->loadFile($dir.$filename));  
                file_put_contents($dir.$cache_filename, $file); 
                return $file;
            }else{
                return $this->loadFile($dir.$cache_filename);
            }
        }else{
            return $this->loadFile($dir.$filename);
        }
    }

    public function getSettings(){
        return $this->page_settings;
    }

    public function getMainTemplate(){
        return $this->active_theme.'\\page.html.twig';
    }
}
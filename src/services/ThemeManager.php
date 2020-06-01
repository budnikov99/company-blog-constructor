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
    private $blocks = [];

    public function __construct() {
        if(!is_null($this->active_theme)){
            return false;
        }
        $theme_settings = Yaml::parseFile(SERVER_ROOT.'/themes/theme.yaml');
        $this->active_theme = $theme_settings['active-theme'];
        $this->theme_dir = SERVER_ROOT.'/themes/'.$this->active_theme;

        $this->theme_data = Yaml::parseFile($this->theme_dir.'/theme.yaml');

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

    public function getMainTemplate(){
        return $this->theme_data['main_template'];
    }

    public function getThemeDir(){
        return $this->theme_dir;
    }
}
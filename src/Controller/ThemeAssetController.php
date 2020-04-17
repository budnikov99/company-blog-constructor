<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use App\services\ThemeManager;

class ThemeAssetController extends AbstractController {
    public function __construct()
    {
        $theme_settings = Yaml::parseFile(SERVER_ROOT.'\\themes\\theme.yaml');
        $this->active_theme = $theme_settings['active-theme'];
        $this->theme_dir = SERVER_ROOT.'\\themes\\'.$this->active_theme.'\\';
    }

    public function loadAssetFile(ThemeManager $theme, $path){
        $asset = $theme->getAssetFile($path);
        if(!is_null($asset)){
            return $asset;
        }else{
            throw $this->createNotFoundException('Requested file does not exist.');
        }
    }

    public function getScript(ThemeManager $theme, $filename){
        $response = new Response();
        $response->headers->set('Content-Type', 'application/javascript');

        $response_file = 'js\\'.$filename;

        $response->setContent($this->loadAssetFile($theme, $response_file));
        return $response;
    }

    public function getStylesheet(ThemeManager $theme, $filename){
        $response = new Response();
        $response->headers->set('Content-Type', 'text/css');

        $asset = $theme->getStyle($filename);
        if(is_null($asset)){
            throw $this->createNotFoundException('Requested file does not exist.');
        }

        $response->setContent($asset);
        return $response;
    }
}
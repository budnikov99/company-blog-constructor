<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use App\services\ThemeManager;

class ThemeAssetController extends AbstractController {
    public $theme = null;
    public function __construct(ThemeManager $theme)
    {
        $this->theme = $theme;
        $theme_settings = Yaml::parseFile(SERVER_ROOT.'\\themes\\theme.yaml');
        $this->active_theme = $theme_settings['active-theme'];
        $this->theme_dir = SERVER_ROOT.'\\themes\\'.$this->active_theme.'\\';
    }

    public function loadAssetFile($path){
        $asset = $this->theme->getAssetFile($path);
        if(!is_null($asset)){
            return $asset;
        }else{
            throw $this->createNotFoundException('Requested file does not exist.');
        }
    }

    public function getAsset($filename){
        $content = $this->loadAssetFile($filename);
        $response = new Response();
        $response->headers->set('Content-Type', $this->theme->getAssetMimeType($filename));

        $response->setContent($content);
        
        return $response;
    }

    public function getScript($filename){
        $response = new Response();
        $response->headers->set('Content-Type', 'application/javascript');

        $response_file = 'js\\'.$filename;

        $response->setContent($this->loadAssetFile($response_file));
        return $response;
    }

    public function getStylesheet($filename){
        $response = new Response();
        $response->headers->set('Content-Type', 'text/css');

        $asset = $this->theme->getStyle($filename);
        if(is_null($asset)){
            throw $this->createNotFoundException('Requested file does not exist.');
        }

        $response->setContent($asset);
        return $response;
    }
}
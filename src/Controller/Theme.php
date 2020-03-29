<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

class Theme extends AbstractController {

    var $active_theme = 'default';
    var $theme_dir = SERVER_ROOT.'\\themes\\default\\';

    public function __construct()
    {
        $theme_settings = Yaml::parseFile(SERVER_ROOT.'\\themes\\theme.yaml');
        $this->active_theme = $theme_settings['active-theme'];
        $this->theme_dir = SERVER_ROOT.'\\themes\\'.$this->active_theme.'\\';
    }

    public function loadFile($path){
        if(file_exists($path)){
            return file_get_contents($path);
        }else{
            throw $this->createNotFoundException('Requested file does not exist.');
        }
    }

    public function getScript($filename){
        $response = new Response();
        $response->headers->set('Content-Type', 'application/javascript');

        $response_file = $this->theme_dir.'script\\'.$filename;

        $response->setContent($this->loadFile($response_file));
        return $response;
    }

    public function getStylesheet($filename){
        $response = new Response();
        $response->headers->set('Content-Type', 'text/css');

        $response_file = $this->theme_dir.'style\\'.$filename;

        $response->setContent($this->loadFile($response_file));
        return $response;
    }

}
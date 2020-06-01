<?php
namespace App\Controller;

use App\Services\AssetManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use App\Services\ThemeManager;

class DynamicAssetController extends AbstractController {
    private $assetm = null;
    public function __construct(AssetManager $assetm)
    {
        $this->assetm = $assetm;
    }

    public function loadAssetFile($path){
        $file = $this->assetm->getAssetFile($path);
        if(!is_null($file)){
            return $file;
        }else{
            throw $this->createNotFoundException('Requested file does not exist.');
        }
    }

    public function getThemeAsset(ThemeManager $theme, $filename){
        $filename = $theme->getThemeDir().'/assets/'.$filename;
        $content = $this->loadAssetFile($filename);
        $response = new Response();
        $response->headers->set('Content-Type', $this->assetm->getAssetMimeType($filename));

        $response->setContent($content);
        
        return $response;
    }

    public function getPluginAsset($plugin, $filename){
        $filename = SERVER_ROOT.'/plugins/'.$plugin.'/assets/'.$filename;
        $content = $this->loadAssetFile($filename);
        $response = new Response();
        $response->headers->set('Content-Type', $this->assetm->getAssetMimeType($filename));

        $response->setContent($content);
        
        return $response;
    }

}
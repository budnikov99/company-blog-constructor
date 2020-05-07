<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UtilAssetController extends AbstractController {
    public function getAsset($filename){
        $filename = SERVER_ROOT.'\\util\\assets\\'.$filename;
        if(file_exists($filename)){
            $response = new Response(file_get_contents($filename));
            $response->headers->set('Content-Type', mime_content_type($filename));
            return $response;
        }else{
            throw $this->createNotFoundException('Requested file does not exist.');
        }
    }
}
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UploadedImageController extends AbstractController {
    public function getImage($filename){
        $filename = SERVER_ROOT.'\\data\\uploads\\img\\'.$filename;

        $response = new Response();
        $response->headers->set('Content-Type', 'image');

        if(file_exists($filename)){
            $response->setContent(file_get_contents($filename));
            return $response;
        }else{
            throw $this->createNotFoundException('Requested file does not exist.');
        }
    }
}
<?php
namespace Plugins\_basic\controllers;

use App\Services\SiteManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SiteSettingsController extends AbstractController {
    public function reinstallDB(SiteManager $sm){
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        
        $sm->setSetting('installed', false);
        $sm->saveSettings();

        return new JsonResponse(['success' => true, 'message' => '']);
    }

    public function applySettings(SiteManager $sm, Request $request){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        
        $data = json_decode($request->getContent(), true);
        if(is_null($data)){
            return new JsonResponse(['success' => false, 'message' => 'Некорректные данные'], Response::HTTP_BAD_REQUEST);
        }

        foreach($data as $name => $value){
            $sm->setPublicSettingValue($name, $value);
        }

        $sm->saveSettings();

        return new JsonResponse(['success' => true, 'message' => '']);
    }

}
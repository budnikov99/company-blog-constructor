<?php
namespace Plugins\_basic\controllers;

use App\Services\SiteManager;
use App\Services\ThemeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemeSettingsController extends AbstractController {
    
    public function applySettings(ThemeManager $tm, Request $request){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        
        $data = json_decode($request->getContent(), true);
        if(is_null($data)){
            return new JsonResponse(['success' => false, 'message' => 'Некорректные данные'], Response::HTTP_BAD_REQUEST);
        }

        foreach($data as $name => $value){
            $tm->setSettingValue($name, $value);
        }

        $tm->saveSettings();

        return new JsonResponse(['success' => true, 'message' => '']);
    }
    
    public function changeTheme(ThemeManager $tm, string $theme){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        
        $tm->setActiveTheme($theme);

        return new JsonResponse(['success' => true, 'message' => '']);
    }

}
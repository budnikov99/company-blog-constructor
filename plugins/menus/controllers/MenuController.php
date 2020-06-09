<?php
namespace Plugins\menus\controllers;

use App\Services\SiteManager;
use App\Services\ThemeManager;
use Plugins\menus\MenuLoader;
use Pluguns\menus\MenuData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class MenuController extends AbstractController {
    
    public function saveMenu(Request $request, string $name){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        $ml = MenuLoader::get();
        
        $data = json_decode($request->getContent(), true);
        if(is_null($data)){
            return new JsonResponse(['success' => false, 'message' => 'Некорректный JSON'], Response::HTTP_BAD_REQUEST);
        }

        $menu = MenuData::deserialize($data);
        if(is_null($menu)){
            return new JsonResponse(['success' => false, 'message' => 'Некорректные данные меню'], Response::HTTP_BAD_REQUEST);
        }

        $ml->setMenu($name, $menu);
        $ml->saveMenus();
        
        return new JsonResponse(['success' => true, 'message' => '']);
    }
    
    public function removeMenu(string $name){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        $ml = MenuLoader::get();
        
        if(!$ml->removeMenu($name)){
            return new JsonResponse(['success' => false, 'message' => 'Меню не существует'], Response::HTTP_BAD_REQUEST);
        }

        $ml->saveMenus();
        return new JsonResponse(['success' => true, 'message' => '']);
    }

}
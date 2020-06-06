<?php
namespace App\Controller;

use App\Services\AdminPanelRenderer;
use App\Services\SiteManager;
use PDO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends AbstractController
{
    public function installSite(SiteManager $sm, Request $request){
        if($sm->isInstalled()){
            return new JsonResponse(['success' => false, 'message' => 'Сайт уже установлен'], Response::HTTP_FORBIDDEN);
        }
        $data = json_decode($request->getContent(), true);
        
        try {
            $sm->updateSettings($data);
        } catch (\Throwable $th) {
            return new JsonResponse(['success' => false, 'message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new RedirectResponse('/install/init');
    }

    public function initSite(SiteManager $sm, Request $request){
        if($sm->isInstalled()){
            return new JsonResponse(['success' => false, 'message' => 'Сайт уже установлен'], Response::HTTP_FORBIDDEN);
        }
        if(!$sm->hasSettings()){
            return new RedirectResponse('/install');
        }
        
        try {
            $sm->installSite();
        } catch (\Throwable $th) {
            return new JsonResponse(['success' => false, 'message' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['success' => true, 'message' => ''], Response::HTTP_OK);
    }
    
    public function checkDatabase(SiteManager $sm, Request $request){
        if($sm->isInstalled()){
            return new JsonResponse(['success' => false, 'message' => 'Сайт уже установлен'], Response::HTTP_FORBIDDEN);
        }
        try{
            $data = json_decode($request->getContent(), true);
            new PDO('mysql:host='.$data['db_host'].';port='.$data['db_port'], $data['db_login'], $data['db_password']);
            return new JsonResponse(['success' => true, 'message' => ''],Response::HTTP_OK);
        } catch (\Throwable $th) {
            return new JsonResponse(['success' => false, 'message' => $th->getMessage()],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getPanel(AdminPanelRenderer $admin, Request $request, $path){
        return new Response($admin->render($path, $request));
    }

    public function getPanelMain(AdminPanelRenderer $admin, Request $request){
        return new Response($admin->render(null, $request));
    }

}
<?php
namespace Plugins\_basic\controllers;

use App\Services\SiteManager;
use App\Services\ThemeManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

class AdminController extends AbstractController {
    
    public function saveAdmin(SiteManager $sm, Request $request){
        $this->denyAccessUnlessGranted('ROLE_ACCOUNTS');
        
        $data = json_decode($request->getContent(), true);
        if(is_null($data)){
            return new JsonResponse(['success' => false, 'message' => 'Некорректные данные'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $data['username']??null;
            if(!$user){
                return new JsonResponse(['success' => false, 'message' => 'Не задано имя пользователя'], Response::HTTP_BAD_REQUEST);
            }
            if(!empty($data['roles']) && in_array('ROLE_SUPER_ADMIN', $data['roles'])){
                return new JsonResponse(['success' => false, 'message' => 'Нельзя выдавать роль ROLE_SUPER_ADMIN'], Response::HTTP_BAD_REQUEST);
            }
            $admin = $sm->getAdminAccount($user);
            var_dump($admin);
            var_dump($user);
            if(!is_null($admin)){
                if(in_array('ROLE_SUPER_ADMIN', $admin->getRoles())){
                    $data['roles'] = null;
                }
                if(empty($data['password'])){
                    $data['password'] = null;
                }
                if(empty($data['roles'])){
                    $data['roles'] = null;
                }
                $sm->updateAdminAccount($user, $data['password'], $data['roles']);
            }else{
                if(empty($data['password'])) {
                    return new JsonResponse(['success' => false, 'message' => 'Не задан пароль'], Response::HTTP_BAD_REQUEST);
                }
                if(empty($data['roles'])) {
                    return new JsonResponse(['success' => false, 'message' => 'Не задан список ролей'], Response::HTTP_BAD_REQUEST);
                }
                $sm->createAdminAccount($data['username'], $data['password'], $data['roles']);
            }
            
        } catch (\Throwable $th) {
            return new JsonResponse(['success' => false, 'message' => 'Произошла ошибка', 'exception' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return new JsonResponse(['success' => true, 'message' => '']);
    }
    
    public function removeAdmin(SiteManager $sm, Security $sec, Request $request){
        $this->denyAccessUnlessGranted('ROLE_ACCOUNTS');
        
        $data = json_decode($request->getContent(), true);
        if(is_null($data)){
            return new JsonResponse(['success' => false, 'message' => 'Некорректные данные'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = $data['username']??null;
            if(!$user){
                return new JsonResponse(['success' => false, 'message' => 'Не задано имя пользователя'], Response::HTTP_BAD_REQUEST);
            }

            if($sec->getUser()->getUsername() == $user){
                return new JsonResponse(['success' => false, 'message' => 'Нельзя удалить свою учётную запись'], Response::HTTP_BAD_REQUEST);
            }

            $admin = $sm->getAdminAccount($user);
            if($admin){
                if(in_array('ROLE_SUPER_ADMIN', $admin->getRoles())){
                    return new JsonResponse(['success' => false, 'message' => 'Нельзя удалить главную учётную запись'], Response::HTTP_BAD_REQUEST);
                }
                $sm->removeAdminAccount($user);
            }
            
        } catch (\Throwable $th) {
            return new JsonResponse(['success' => false, 'message' => 'Произошла ошибка', 'exception' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['success' => true, 'message' => '']);
    }

}
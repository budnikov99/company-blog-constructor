<?php
namespace Plugins\_basic\controllers;

use App\Services\Data\PageData;
use App\Services\PluginManager;
use App\Services\PageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;

class PageEditController extends AbstractController {

    private function getArgument(PluginManager $mm, $module_name, $argument_name){
        $module = $mm->getModule($module_name);
        if(!is_null($module)){
            return $module->getArgument($argument_name);
        }
        return null;
    }

    public function validateModuleArgument(PluginManager $mm, Request $request, $module_name, $arg_name){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        $value = $request->query->get('value');

        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');

        $arg = $this->getArgument($mm, $module_name, $arg_name);
        if($arg){
            $response->setContent($arg->validate($value)?'true':'false');
        }else{
            throw $this->createNotFoundException('Модуль или аргумент не существует.');
        }
        
        return $response;
    }

    public function getModuleArgumentList(PluginManager $mm, $module_name, $arg_name){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');

        $arg = $this->getArgument($mm, $module_name, $arg_name);
        if($arg){
            $response->setContent(json_encode(['list' => $arg->values()]));
        }else{
            throw $this->createNotFoundException('Модуль или аргумент не существует.');
        }
        
        return $response;
    }

    public function updatePage(PageManager $pm, Request $request, $page_id){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        $data = [
            'success' => true,
            'message' => 'Успешно.',
        ];

        $page = PageData::deserialize(json_decode($request->getContent(), true));
        if(is_null($page)){
            $data['success'] = false;
            $data['message'] = 'Некорректные данные.';
        }else if(!$pm->pageExists($page_id)){
            $data['success'] = false;
            $data['message'] = 'Страница не существует.';
        }else if(!$pm->savePage($page_id, $page)){
            $data['success'] = false;
            $data['message'] = 'Неизвестная ошибка.';
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }

    public function removePage(PageManager $pm, $page_id){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        $data = [
            'success' => true,
            'message' => 'Успешно.',
        ];
        if(!$pm->pageExists($page_id)){
            $data['success'] = false;
            $data['message'] = 'Страница не существует.';
        }else if(!$pm->removePage($page_id)){
            $data['success'] = false;
            $data['message'] = 'Неизвестная ошибка.';
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }

    public function validatePageName(PageManager $pm, $page_id){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        $response = new Response($pm->isPageidValid($page_id)?'true':'false');
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }

    public function isPageExists(PageManager $pm, $page_id){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        $response = new Response($pm->pageExists($page_id)?'true':'false');
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }

    public function createPage(PageManager $pm, $page_id){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        $data = [
            'success' => true,
            'message' => 'Успешно.',
        ];
        if(!$pm->isPageidValid($page_id)){
            $data['success'] = false;
            $data['message'] = 'Некорректный адрес страницы.';
        }else if($pm->pageExists($page_id)){
            $data['success'] = false;
            $data['message'] = 'Страница уже существует.';
        }else if(!$pm->createPage($page_id)){
            $data['success'] = false;
            $data['message'] = 'Неизвестная ошибка.';
        }

        $response = new Response(json_encode($data));
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }

    public function getPage(PageManager $pm, $page_id){
        $this->denyAccessUnlessGranted('ROLE_CONSTRUCTOR');
        if(!$pm->pageExists($page_id)){
            throw $this->createNotFoundException('Страница не существует.');
        }

        $page = $pm->getCorrectedPage($page_id);
        if(is_null($page)){
            throw new InternalErrorException('Произошла ошибка');
        }

        $json = json_encode($page->serialize());
        $response = new Response($json);
        $response->headers->set('Content-Type', 'text/json');
        return $response;
    }

}
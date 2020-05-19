<?php
namespace App\Controller;

use App\services\data\BlockData;
use App\services\data\PageData;
use App\services\ModuleManager;
use App\services\PageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Yaml\Yaml;

class ConstructorController extends AbstractController {


    private function getArgument(ModuleManager $mm, $module_name, $argument_name){
        $module = $mm->getModule($module_name);
        if(!is_null($module)){
            return $module->getArgument($argument_name);
        }
        return null;
    }

    public function validateModuleArgument(ModuleManager $mm, Request $request, $module_name, $arg_name){
        $value = $request->query->get('value');

        $response = new Response();
        $response->headers->set('Content-Type', 'text/json');

        $arg = $this->getArgument($mm, $module_name, $arg_name);
        if($arg){
            $response->setContent(json_encode(['valid' => $arg->validate($value)]));
        }else{
            throw $this->createNotFoundException('Модуль или аргумент не существует.');
        }
        
        return $response;
    }

    public function getModuleArgumentList(ModuleManager $mm, $module_name, $arg_name){
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
        $page = PageData::deserialize(json_decode($request->getContent(), true));
        if(is_null($page)){
            throw new BadRequestHttpException('Некорректные данные.');
        }
        if(!$pm->pageExists($page_id)){
            throw $this->createNotFoundException('Страница не существует.');
        }
   
        if(!$pm->savePage($page_id, $page)){
            throw new InternalErrorException('Произошла ошибка');
        }

        return new Response();
    }

    public function removePage(PageManager $pm, $page_id){
        if(!$pm->pageExists($page_id)){
            throw $this->createNotFoundException('Страница не существует.');
        }
   
        if(!$pm->removePage($page_id)){
            throw new InternalErrorException('Произошла ошибка');
        }

        return new Response();
    }

    public function createPage(PageManager $pm, $page_id){
        if($pm->pageExists($page_id)){
            throw $this->createNotFoundException('Страница уже существует.');
        }
   
        if(!$pm->createPage($page_id)){
            throw new InternalErrorException('Произошла ошибка');
        }

        return new Response();
    }

}
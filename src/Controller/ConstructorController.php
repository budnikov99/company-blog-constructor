<?php
namespace App\Controller;

use App\services\data\BlockData;
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

    public function setBlock(PageManager $pm, Request $request, $page_id, $block_name){
        $block = BlockData::deserialize(json_decode($request->getContent(), true));
        if(is_null($block)){
            throw new BadRequestHttpException('Некорректные данные.');
        }
        if(!$pm->pageExists($page_id)){
            throw $this->createNotFoundException('Страница не существует.');
        }
   
        if(!$pm->saveBlockToConfig($page_id, $block_name, $block)){
            throw new InternalErrorException('Произошла ошибка');
        }

        return new Response();
    }

    public function removeBlock(PageManager $pm, Request $request, $page_id, $block_name){
        if(!$pm->pageExists($page_id)){
            throw $this->createNotFoundException('Страница не существует.');
        }
   
        if(!$pm->saveBlockToConfig($page_id, $block_name, null)){
            throw new InternalErrorException('Произошла ошибка');
        }

        return new Response();
    }

}
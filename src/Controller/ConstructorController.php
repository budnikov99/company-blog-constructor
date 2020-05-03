<?php
namespace App\Controller;

use App\services\ModuleManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConstructorController {


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
            $response->setStatusCode(404);
            $response->setContent(json_encode(['valid' => 'error', 'message' => 'wrong module or argument']));
            return $response;
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
            $response->setStatusCode(404);
            $response->setContent(json_encode(['list' => 'error', 'message' => 'wrong module or argument']));
            return $response;
        }
        
        return $response;
    }

    public function getModuleData(ModuleManager $mm){
        $data = array();

        foreach($mm->getModuleList() as $module_name => $module){
            $module_data = [
                'name' => $module_name,
                'format' => $module->getFormat(),
                'title' => $module->getTitle(),
                'args' => array(),
            ];
            foreach($module->getArgumentList() as $arg_name => $arg){
                $arg_data = [
                    'name' => $arg_name,
                    'type' => $arg->getType(),
                    'title' => $arg->getTitle(),
                    'required' => $arg->isRequired(),
                    'list' => null,
                ];

                if($arg->hasValueList()){
                    $arg_data['list'] = $arg->values();
                }

                $module_data['args'][$arg_name] = $arg_data;
            }

            $data[$module_name] = $module_data;
        }

        $response = new Response();

        $response->headers->set('Content-Type', 'text/json');
        $response->setContent(json_encode($data));

        return $response;
    }

    public function getBlockData(){
        
    }

}
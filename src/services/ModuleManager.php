<?php
namespace App\services;

use Symfony\Component\Yaml\Yaml;
use App\modules\Module;
use App\modules\ModuleException;

class ModuleManager {
    private $module_list = null;

    public function __construct(){
        $this->module_list = array();

        $modules_dir = SERVER_ROOT.'\\src\\modules';

        $module_dir_files = scandir($modules_dir);
        //Чтение подпапок папки src/modules
        foreach($module_dir_files as $dirname){
            $dir = $modules_dir.'\\'.$dirname;
            if(is_dir($dir)){
                //Чтение конфига module.yaml и наполнение списка классами модулей
                if(file_exists($dir.'\\module.yaml')){
                    $module_data = Yaml::parseFile($dir.'\\module.yaml');
                    
                    foreach($module_data as $key => $value){
                        $classname = $value['class'];    //Имя класса модуля
                        $classname = 'App\\modules\\'.$dirname.'\\'.$classname;
                        $format = $value['format'];     //Возвращаемый формат модуля
                        $arglist = $value['args'];
                        $title = $value['title'] ?: $key;

                        $module = new $classname($key, $title, $format, $arglist);

                        if(!is_subclass_of($module, Module::class)){
                            throw new ModuleException('Модуль '.$classname.' не унаследован от Module.');
                        }
                        
                        $this->module_list[$key] = $module;
                    }
                }
            }
        }
    }

    public function getModule($module){
        if(array_key_exists($module, $this->module_list)){
            return $this->module_list[$module];
        }
        return null;
    }

    public function getModuleList(){
        return $this->module_list;
    }
}

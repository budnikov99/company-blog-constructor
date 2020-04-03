<?php
namespace App\services;

use Symfony\Component\Yaml\Yaml;

class ModuleManager {
    private $module_list = null;

    public function __construct(){
        $this->module_list = array();

        $modules_dir = SERVER_ROOT.'\\src\\modules';

        $module_dir_files = scandir($modules_dir);
        foreach($module_dir_files as $dir){
            if(is_dir($dir)){
                $dir = $modules_dir.'\\'.$dir;
                if(file_exists($dir.'\\module.yaml')){
                    $module_data = Yaml::parseFile($dir.'\\module.yaml');

                    

                }
            }
        }
    }

}

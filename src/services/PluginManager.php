<?php
namespace App\Services;

use App\Plugins\AdminPanelExtension;
use Symfony\Component\Yaml\Yaml;
use App\Plugins\Module;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class PluginManager extends Manager {
    private $module_list = [];
    private $adminpanel_extensions = [];

    public function __construct(
        AssetManager $am, 
        PageManager $pgm, 
        SiteManager $sm, 
        ThemeManager $tm,
        PostManager $psm
    ){
        $plugin_dir = SERVER_ROOT.'/plugins';

        $plugins = scandir($plugin_dir);

        $managers = [
            AssetManager::class => $am,
            PageManager::class => $pgm,
            PluginManager::class => $this,
            SiteManager::class => $sm,
            ThemeManager::class => $tm,
            PostManager::class => $psm,
        ];

        foreach($plugins as $pname){
            if(in_array($pname, ['.', '..'])){
                continue;
            }
            $dir = $plugin_dir.'/'.$pname;
            if(is_dir($dir)){
                $this->loadPluginModules($pname, $managers);             
                $this->loadAdminPanelExtensions($pname, $managers);   
            }
        } 
    }

    private function loadPluginModules($plugin, $managers){
        $dir = SERVER_ROOT.'/plugins/'.$plugin.'/modules/';

        if(file_exists($dir.'modules.yaml')){
            $module_data = Yaml::parseFile($dir.'modules.yaml')??[];
            
            foreach($module_data as $key => $value){
                $key = $plugin.'/'.$key;
                $classname = $value['class'];    
                $classname = 'Plugins\\'.$plugin.'\\modules\\'.$classname;
                $format = $value['format'];     
                $arglist = $value['args'];
                $title = $value['title'] ?: $key;

                if(!class_exists($classname)){
                    StaticLogger::error('Класс, указанный в модуле не существует', [
                        'module' => $key, 
                        'class' => $classname
                    ]);
                    continue;
                }

                $module = new $classname($key, $title, $format, $arglist, $managers);

                if(!is_subclass_of($module, Module::class)){
                    StaticLogger::error('Модуль не унаследован от Module.', [
                        'extension' => $key, 
                        'class' => $classname
                    ]);
                    continue;
                }
                
                $this->module_list[$key] = $module;
            }
        }
    }

    private function loadAdminPanelExtensions($plugin, $managers){
        $dir = SERVER_ROOT.'/plugins/'.$plugin.'/adminpanel/';

        if(file_exists($dir.'adminpanel.yaml')){
            $apanel_data = Yaml::parseFile($dir.'adminpanel.yaml')??[];
            foreach($apanel_data as $name => $value){
                $classname = $value['class'];    
                $classname = 'Plugins\\'.$plugin.'\\adminpanel\\'.$classname;;
                $title = $value['title'] ?: $name;

                if(!class_exists($classname)){
                    StaticLogger::error('Класс, указанный в расширении админпанели не существует', [
                        'extension' => $plugin.'/'.$name, 
                        'class' => $classname
                    ]);
                    continue;
                }

                $extension = new $classname($plugin, $name, $title, $value['roles']??[], $managers);

                if(!is_subclass_of($extension, AdminPanelExtension::class)){
                    StaticLogger::error('Расширение не унаследовано от AdminPanelExtension.', [
                        'extension' => $plugin.'/'.$name, 
                        'class' => $classname
                    ]);
                    continue;
                }
                
                $this->adminpanel_extensions[$plugin.'/'.$name] = $extension;
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

    public function getExtension($ext){
        if(array_key_exists($ext, $this->adminpanel_extensions)){
            return $this->adminpanel_extensions[$ext];
        }
        return null;
    }

    public function getExtensionList(){
        return $this->adminpanel_extensions;
    }

    private function createRoute(string $id, array $data, string $id_pref, string $route_pref, string $namespace_pref){
        if(empty($data['path']) || empty($data['controller'])){
            StaticLogger::warning('Ошибка в route плагина', [
                'plugin' => $id_pref,
                'route' => $id
            ]);
            return null;
        }
        $id = $id_pref.$id;
        $path = $data['path'][0] == '/' ? substr($data['path'], 1):$data['path'];
        if(strlen($path) == 0){
            StaticLogger::warning('Ошибка в route плагина', [
                'plugin' => $id_pref,
                'route' => $id
            ]);
            return null;
        }
        $path = $route_pref.$path;

        $controller = explode('::', $data['controller']);
        if(count($controller) != 2){
            StaticLogger::warning('Ошибка в route плагина', [
                'plugin' => $id_pref,
                'route' => $id
            ]);
            return null;
        }
        $controller[0] = $namespace_pref.$controller[0];
        if(!method_exists($controller[0], $controller[1])){
            StaticLogger::warning('Ошибка в route плагина. Метод или контроллер не существует', [
                'plugin' => $id_pref,
                'route' => $id,
                'class' => $controller[0],
                'method' => $controller[1]
            ]);
            return null;
        }
        return new \Symfony\Component\Routing\Route(
            $path, 
            ['_controller' => $controller[0].'::'.$controller[1]],
            $data['requirements']??[],
            $data['options']??[],
            $data['host']??'',
            $data['schemes']??[],
            $data['methods']??[],
            $data['condition']??null                        
        );
    }

    public function loadPluginRoutes(){
        $routes = new \Symfony\Component\Routing\RouteCollection();

        $plugin_dir = SERVER_ROOT.'/plugins';

        $plugins = scandir($plugin_dir);

        foreach($plugins as $pname){
            if(in_array($pname, ['.', '..'])){
                continue;
            }
            $dir = $plugin_dir.'/'.$pname;
            if(is_dir($dir)){
                if(file_exists($dir.'/controllers/routes.yaml')){
                    $route_data = Yaml::parseFile($dir.'/controllers/routes.yaml')??[];
                    foreach($route_data as $id => $data){
                        $route = $this->createRoute($id, $data, $pname.'_', 'plugin/'.$pname.'/', '\\Plugins\\'.$pname.'\\controllers\\');
                        if($route){
                            $routes->add($id, $route);
                        }
                    }
                }
                if(file_exists($dir.'/public_controllers/routes.yaml')){
                    $route_data = Yaml::parseFile($dir.'/public_controllers/routes.yaml')??[];

                    foreach($route_data as $id => $data){
                        $route = $this->createRoute($id, $data, $pname.'_public_', 'public/plugin/'.$pname.'/', '\\Plugins\\'.$pname.'\\public_controllers\\');
                        if($route){
                            $routes->add($id, $route);
                        }
                    }
                }
            }
        }
        
        return $routes;
    }
}

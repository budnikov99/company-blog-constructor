<?php
namespace App\services;

use App\content\Content;
use App\services\data\ModuleData;
use App\services\data\PageData;
use Twig\Environment;
use \Twig\Loader\FilesystemLoader;
use Psr\Log\LoggerInterface;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;

class PageRenderer {
    private $themem = null;
    private $plugins = null;
    private $pagem = null;
    private $twig = null;

    public function __construct(Environment $twig, ThemeManager $themem, PluginManager $plugins, PageManager $pagem){
        $this->themem = $themem;
        $this->modules = $plugins;
        $this->pagem = $pagem;

        $this->twig = $twig;
        $loader = $this->twig->getLoader();
        if($loader instanceof FilesystemLoader){
            $loader->addPath($themem->getThemeDir());
        }
        if($_SERVER['APP_DEBUG']){
            $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        }
    }

    private function renderBlockData($page_data){
        $theme_data = $this->themem->getBlocks();
        
        foreach($page_data->getBlocks() as $name => $block){
            $block_theme = $theme_data[$name];
            $formats = $block_theme->getAcceptedFormats();

            if($block->getActive()){
                foreach($block->getModules() as $module){

                    if(is_null($this->modules->getModule($module->getName()))){
                        StaticLogger::error('На странице запрошен несуществующий модуль', [
                            'module' => $module->getName(),
                            'block' => $name
                        ]);
                        $block->setActive(false);
                        continue;
                    }

                    if(in_array($this->modules->getModule($module->getName())->getFormat(), $formats)){
                        $module->setData($this->modules->getModule($module->getName())->getData($module->getArgs()));
                    }else{
                        StaticLogger::error('Блок не поддерживает формат данных модуля ', [
                            'block' => $name,
                            'module' => $module->getName(),
                            'format' => $this->modules->getModule($module->getName())->getFormat()
                        ]);
                    }
                }
            }
        }
    }

    public function getPageData($pageid){
        if(!$this->pagem->pageExists($pageid)){
            return null;
        }

        return $this->pagem->getPageData($pageid);
    }

    public function generateTemplateData(string $pageid, PageData $page_data){
        $this->renderBlockData($page_data);

        $data = [
            'page_id' => $pageid,
            'data' => $page_data,            
        ];

        return $data;
    }

    public function getPage($pageid){
        $page = $this->getPageData($pageid);
        if(is_null($page)){
            throw new NotFoundHttpException();
        }

        //Контент для технических страниц генерируется отдельно
        if($pageid[0] != '_'){
            $page->createContentFromArgs();
            if(is_null($page->getContent()) || !$page->getContent()->isLoaded()){
                throw new InternalErrorException('Ошибка при загрузке контента');
            }
        }
    
        return $this->renderPage($this->generateTemplateData($pageid, $page));
    }

    public function getIndexPage(){
        return $this->getPage('index');
    }

    public function renderPage($data){
        $template_name = $this->themem->getMainTemplate();
        return $this->twig->render($template_name, $data);
    }

}

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
    private $modules = null;
    private $pagem = null;
    private $twig = null;
    private $logger = null;

    public function __construct(ThemeManager $themem, ModuleManager $modules, LoggerInterface $logger, PageManager $pagem){
        $this->themem = $themem;
        $this->modules = $modules;
        $this->pagem = $pagem;

        $loader = new FilesystemLoader($themem->getThemeDir());
        $loader->addPath(SERVER_ROOT.'\\util');
        $this->twig = new Environment($loader, ['debug' => $_SERVER['APP_DEBUG']]);
        if($_SERVER['APP_DEBUG']){
            $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        }
        $this->logger = $logger;
    }

    private function renderBlockData($page_data){
        $theme_data = $this->themem->getBlocks();
        
        foreach($page_data->getBlocks() as $name => $block){
            $block_theme = $theme_data[$name];
            $formats = $block_theme->getAcceptedFormats();

            if($block->getActive()){
                foreach($block->getModules() as $module){

                    if(is_null($this->modules->getModule($module->getName()))){
                        $this->logger->error('Несущетсвующий модуль '.$module.' запрошен в блоке '.$name);
                        continue;
                    }

                    if(in_array($this->modules->getModule($module->getName())->getFormat(), $formats)){
                        $module->setData($this->modules->getModule($module->getName())->getData($module->getArgs()));
                    }else{
                        $this->logger->error('Блок '.$name.' не поддерживает формат данных модуля '.$module->getName().
                                ' ('.$this->modules->getModule($module->getName())->getFormat().')');
                    }
                }
            }
        }
    }

    public function getConstructorData(string $pageid, PageData $page_data){

        $current_page = $this->pagem->correctPage($this->pagem->loadPage($pageid));
        $global_page = $this->pagem->getGlobalPage();

        $modules_serialized = [];
        foreach($this->modules->getModuleList() as $key => $value){
            $modules_serialized[$key] = ModuleData::createFromModule($value)->serialize();
        }

        return [
            'current_page' => $current_page,
            'page_json' => json_encode($current_page->serialize()),
            'modules_json' => json_encode($modules_serialized),

            'blocks' => $this->themem->getBlocks(),
            'modules' => $this->modules->getModuleList(),
            'global_page' => $global_page,
            'page_list' => $this->pagem->getPageList(),
        ];
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
            'id' => $pageid,
            'data' => $page_data,            
        ];

        if(true){
            $data['constructor'] = $this->getConstructorData($pageid, $page_data);
        }

        return $data;
    }

    public function getPage($pageid){
        $page = $this->getPageData($pageid);

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
        $page = $this->getPageData('_index');
        $page->createContentFromArgs();

        return $this->renderPage($this->generateTemplateData('_index', $page));
    }

    public function renderPage($data){
        $template_name = $this->themem->getMainTemplate();
        return $this->twig->render($template_name, $data);
    }

}

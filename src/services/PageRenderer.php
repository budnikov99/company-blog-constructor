<?php
namespace App\services;

use Twig\Environment;
use \Twig\Loader\FilesystemLoader;
use Psr\Log\LoggerInterface;
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
        $loader->addPath(SERVER_ROOT.'\\constructor');
        $this->twig = new Environment($loader, ['debug' => $_SERVER['APP_DEBUG']]);
        if($_SERVER['APP_DEBUG']){
            $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        }
        $this->logger = $logger;
    }

    private function renderBlockData($block_data){
        $theme_data = $this->themem->getThemeData();

        $blocks_data = array();

        foreach($block_data as $name => $block){
            $block_theme = $theme_data['blocks'][$name];
            $formats = $block_theme['accepted_formats'];

            if($block->isActive()){
                foreach($block->getModules() as $module){

                    if(is_null($this->modules->getModule($module->getName()))){
                        $this->logger->error('Несущетсвующий модуль '.$module.' запрошен в блоке '.$name);
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


    public function getPage($pageid){
        $page_data = $this->pagem->getPageData($pageid);
        $this->renderBlockData($page_data->getBlocks());

        $template_name = $this->themem->getMainTemplate();

        return $this->twig->render($template_name, ['data' => $page_data]);
    }

}

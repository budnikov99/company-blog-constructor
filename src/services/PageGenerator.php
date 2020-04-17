<?php
namespace App\services;

use Twig\Environment;
use Psr\Log\LoggerInterface;

class PageGenerator {
    private $theme = null;
    private $modules = null;
    private $twig = null;
    private $logger = null;

    public function __construct(ThemeManager $theme, ModuleManager $modules, Environment $twig, LoggerInterface $logger){
        $this->theme = $theme;
        $this->modules = $modules;
        $this->twig = $twig;
        $this->logger = $logger;
    }

    private function generateBlockData(){
        $settings = $this->theme->getSettings();

        $blocks_data = array();

        foreach($settings['blocks'] as $name => $data){
            $args = $data['module'];
            $formats = $data['accepted-formats'];
            $module = $args['module'];

            $block_data = array('active' => $data['active']);     

            if($data['active'] && !is_null($module)){
                if(in_array($this->modules->getModule($module)->getFormat(), $formats)){
                    $block_data[$name] = $this->modules->getModule($module)->getData($args);
                }else{
                    $this->logger->error('Блок '.$name.' не поддерживает формат данных модуля '.$module.
                            ' ('.$this->modules->getModule($module)->getFormat().')');
                    
                    $block_data['active'] = false;
                }
            }

            $blocks_data[$name] = $block_data;
        }

        return $blocks_data;
    }

    public function getPage(){
        $data = array(
            'blocks' => $this->generateBlockData(),
            'content' => 'Hello world!',
        );

        $template_name = $this->theme->getMainTemplate();

        return $this->twig->render($template_name, $data);
    }

}
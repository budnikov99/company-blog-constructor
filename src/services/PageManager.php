<?php
namespace App\services;

use App\services\data\BlockData;
use App\services\data\BlockModuleData;
use App\services\data\PageData;
use Symfony\Component\Yaml\Yaml;

class PageManager {
    private $blocks_global = null;
    private $settings = null;

    public function __construct(){
        $this->blocks_global = Yaml::parseFile(SERVER_ROOT.'\\data\\blocks_global.yaml');
        $this->settings = Yaml::parseFile(SERVER_ROOT.'\\data\\settings.yaml');
    }

    /**
     * Создаёт пустую главную страницу
     *
     * @return void
     */
    public static function generateIndexPage(){
        file_put_contents(SERVER_ROOT.'\\data\\pages\\index.yaml', Yaml::dump([
            'title' => 'Главная',
            'page_content' => [
                'type' => 'static',
                'file' => 'index.html'
            ],
            'blocks_override' => null
        ]));
        file_put_contents(SERVER_ROOT.'\\data\\pages\\index.html', '<h1>Это шаблон главной страницы. Используйте конструктор, чтобы изменить её.</h1>');
        file_put_contents(SERVER_ROOT.'\\data\\pages\\_post.yaml', Yaml::dump(['blocks_override' => null]));
    }

    public function pageExists($pageid){
        return file_exists(SERVER_ROOT.'\\data\\pages\\'.$pageid.'.yaml');
    }

    public static function getPageDir(){
        return SERVER_ROOT.'\\data\\pages\\';
    }

    public function getPageData($pageid){
        $page_dir = $this->getPageDir();
        $block_data = array();
        
        $page = Yaml::parseFile($page_dir.$pageid.'.yaml');

        foreach($this->blocks_global as $block_name => $block_global){
            $block = $block_global;
            if(array_key_exists('block_override', $page) && is_array($page['block_override']) 
               && array_key_exists($block_name, $page['block_override']))
            {
                $block = $page['block_override'][$block_name];
            }

            $bdata = new BlockData($block_name, $block['active']);
            foreach($block['modules'] as $module){
                $bdata->addModule(new BlockModuleData($module['module'], $module));
            }

            $block_data[$block_name] = $bdata;
        }

        $pdata = new PageData($page['title']);
        $pdata->setFavicon($page['favicon'] ?? $this->settings['favicon']);
        $pdata->setBlockList($block_data);
        $page_content = $page['page_content'];
        $pdata->setContent($page_content['type'], $page_content);

        return $pdata;
    }



}
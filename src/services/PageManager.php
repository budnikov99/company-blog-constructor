<?php
namespace App\services;

use App\content\Content;
use App\services\data\BlockData;
use App\services\data\BlockModuleData;
use App\services\data\PageData;
use Exception;
use Symfony\Component\Yaml\Yaml;

class PageManager {
    private $themem = null;
    private $global_settings = null;

    public function __construct(ThemeManager $themem){
        $this->themem = $themem;
        $this->global_settings = $this->loadGlobalPage();
        if(is_null($this->global_settings)){
            throw new Exception('Ошибка в файле глобальной конфигурации _global.yaml');
        }
    }

    public function pageExists($pageid){
        return file_exists($this->getPageDir().$pageid.'.yaml');
    }

    public static function getPageDir(){
        return SERVER_ROOT.'\\data\\pages\\';
    }

    private function loadGlobalPage(){
        $data = PageData::deserialize(Yaml::parseFile(SERVER_ROOT.'\\data\\pages\\_global.yaml'));

        $theme_blocks = $this->themem->getThemeData()['blocks'];

        //Удаление блоков, не поддерживаемых темой
        foreach($data->getBlocks() as $block_name => $_){
            if(!array_key_exists($block_name, $theme_blocks)){
                $data->removeBlock($block_name);
            }
        }

        //Добавление пустых блоков из темы, которые отсутствуют в глобальной конфигурации
        foreach($theme_blocks as $block_name => $_){
            if(is_null($data->getBlock($block_name))){
                $data->setBlock($block_name, new BlockData(false));
            }
        }

        return $data;
    }

    public function loadPage(string $pageid){
        if(!$this->pageExists($pageid)){
            return null;
        }

        return PageData::deserialize(Yaml::parseFile($this->getPageDir().$pageid.'.yaml'));
    }

    public function savePage(string $pageid, PageData $page){
        if(!$this->pageExists($pageid) || is_null($page)){
            return false;
        }

        $pagedata = $page->serialize();
        file_put_contents($this->getPageDir().$pageid.'.yaml', Yaml::dump($pagedata));
        return true;
    }

    /**
     * Записывает или удаляет блок в конфиг страницы. 
     *
     * @param string $pageid - id страницы
     * @param string $block_name - название блока
     * @param BlockData|null $block - данные блока. Если передано null, блок удаляется
     * @return boolean - true, если блок был записан, false в остальных случаях.
     */
    public function saveBlockToConfig(string $pageid, string $block_name, BlockData $block = null){
        $page = $this->loadPage($pageid);

        if(is_null($page)){
            return false;
        }

        //Удаление блока
        if(is_null($block)){
            if(!is_null($page->getBlock($block_name))){
                $page->removeBlock($block_name);
            }else{
                //Блока уже не существует
                return false;
            }
        //Добавление блока
        }else{
            if(array_key_exists($block_name, $this->global_settings->getBlocks())){
                $page->setBlock($block_name, $block);
            }else{
                //Блок не существует в теме, добавление не имеет смысла
                return false;
            }
        }

        return $this->savePage($pageid, $page);

    }

    public function getPageData($pageid){
        $global = $this->global_settings;

        if(!$this->pageExists($pageid)){
            return null;
        }

        $page = $this->loadPage($pageid);
        if(is_null($page)){
            return null;
        }

        if(is_null($page->getFavicon())){
            $page->setFavicon($global->getFavicon());
        }  

        foreach($global->getBlocks() as $block_name => $block){
            if(is_null($page->getBlock($block_name))){
                $page->setBlock($block_name, $block);
            }
        }

        return $page;
    }

}
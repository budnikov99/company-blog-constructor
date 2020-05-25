<?php
namespace App\services;

use App\content\Content;
use App\services\data\BlockData;
use App\services\data\BlockModuleData;
use App\services\data\PageData;
use Exception;
use Symfony\Component\Yaml\Yaml;

class PageManager extends Manager {
    private $themem = null;
    private $global_settings = null;
    private $page_list = [];

    public function __construct(ThemeManager $themem){
        $this->themem = $themem;
        $this->global_settings = $this->loadGlobalPage();
        if(is_null($this->global_settings)){
            StaticLogger::critical('Ошибка в файле глобальной конфигурации страниц _global.yaml');
            throw new Exception('Ошибка в файле глобальной конфигурации страниц _global.yaml');
        }
        foreach(scandir($this->getPageDir()) as $file){
            if(substr($file, -5) == '.yaml'){
                $this->page_list []= substr($file, 0, -5);
            }
        }
    }

    public function pageExists($pageid){
        return file_exists($this->getPageDir().$pageid.'.yaml');
    }

    public static function getPageDir(){
        return SERVER_ROOT.'/data/pages/';
    }

    private function loadGlobalPage(){
        $data = PageData::deserialize(Yaml::parseFile(SERVER_ROOT.'/data/pages/_global.yaml'));

        $theme_blocks = $this->themem->getBlocks();

        //Удаление блоков, не поддерживаемых темой и корректировка активности
        foreach($data->getBlocks() as $block_name => $block){
            if(!array_key_exists($block_name, $theme_blocks)){
                $data->removeBlock($block_name);
            } else if(is_null($block->getActive())) {
                //Блок на глобальной странице не может быть унаследован из неё же
                $block->setActive(false);
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
        if(is_null($page)){
            return false;
        }

        $pagedata = $page->serialize();
        file_put_contents($this->getPageDir().$pageid.'.yaml', Yaml::dump($pagedata));
        return true;
    }

    public function isPageidValid($page_id){
        if(empty($page_id) ||  gettype($page_id) != 'string' || $page_id[0] == '_'){
            return false;
        }

        for($i = 0; $i < strlen($page_id); $i++){
            $char = $page_id[$i];
            if(in_array($char, [
                ' ', '/', '\\', ':', '*', '?', '\"', '\'', '<', '>', '|'
            ])){
                return false;
            }
        }

        $reserved = [
            'admin',
            'assets',
            'plugin',
            'plugins',
        ];
        if(in_array($page_id, $reserved)){
            return false;
        }

        return true;
    }

    public function getBlankPage(){
        $data = new PageData('Новая страница', 'static');
        $data->setContentArgs(['content' => '']);
        return $this->correctPage($data);
    }

    public function createPage(string $pageid){
        if($this->pageExists($pageid) || !$this->isPageidValid($pageid)){
            return false;
        }

        file_put_contents($this->getPageDir().$pageid.'.yaml', Yaml::dump($this->getBlankPage()->serialize()));
        return true;
    }

    public function removePage($pageid){
        //Нельзя удалять технические и не существующие страницы
        if(!$this->pageExists($pageid) || $pageid[0]=='_'){
            return false;
        }

        unlink($this->getPageDir().$pageid.'.yaml');
        return true;
    }

    public function correctPage(PageData $page){
        if(is_null($page)){
            return null;
        }

        foreach($this->global_settings->getBlocks() as $block_name => $block){
            if(is_null($page->getBlock($block_name))){
                $page->setBlock($block_name, new BlockData(null));
            }else if(count($page->getBlock($block_name)->getModules()) == 0){
                $page->getBlock($block_name)->setActive(null);
            }
        }

        return $page;
    }

    public function getCorrectedPage($pageid){
        return $this->correctPage($this->loadPage($pageid));
    }

    public function getPageData($pageid){
        if(!$this->pageExists($pageid)){
            return null;
        }

        $page = $this->correctPage($this->loadPage($pageid));
        if(is_null($page)){
            return null;
        }

        //Заменяем унаследованные блоки на блоки из глобальной конфигурации
        foreach($page->getBlocks() as $block_name => $block){
            if(is_null($block->getActive())){
                $page->setBlock($block_name, $this->global_settings->getBlock($block_name));
            }
        }

        return $page;
    }

    public function getGlobalPage(){
        return $this->global_settings;
    }

    public function getPageList(){
        return $this->page_list;
    }

}
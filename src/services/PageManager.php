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
    private $page_list = [];

    public function __construct(ThemeManager $themem){
        $this->themem = $themem;
        $this->global_settings = $this->loadGlobalPage();
        if(is_null($this->global_settings)){
            throw new Exception('Ошибка в файле глобальной конфигурации _global.yaml');
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
        return SERVER_ROOT.'\\data\\pages\\';
    }

    private function loadGlobalPage(){
        $data = PageData::deserialize(Yaml::parseFile(SERVER_ROOT.'\\data\\pages\\_global.yaml'));

        $theme_blocks = $this->themem->getBlocks();

        //Удаление блоков, не поддерживаемых темой
        foreach($data->getBlocks() as $block_name => $block){
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
        if(is_null($page)){
            return false;
        }

        $pagedata = $page->serialize();
        file_put_contents($this->getPageDir().$pageid.'.yaml', Yaml::dump($pagedata));
        return true;
    }

    public function createPage(string $pageid){
        if($this->pageExists($pageid)){
            return false;
        }

        file_put_contents($this->getPageDir().$pageid.'.yaml', Yaml::dump([
            'title' => $pageid,
            'page_content' => [
                'type' => 'static',
                'content' => '<h1>Это шаблон страницы. Используйте <a href="/admin/login">конструктор</a>, чтобы изменить её.</h1>'
            ],
            'blocks' => null
        ]));
        return true;
    }

    public function removePage($pageid){
        //Нельзя удалять технические страницы
        if(!$this->pageExists($pageid) || $pageid[0]=='_'){
            return false;
        }

        unlink($this->getPageDir().$pageid.'.yaml');
        return true;
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

    public function getGlobalPage(){
        return $this->global_settings;
    }

    public function getPageList(){
        return $this->page_list;
    }

}
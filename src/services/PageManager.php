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
        $this->global_settings = PageData::deserialize(Yaml::parseFile(SERVER_ROOT.'\\data\\pages\\_global.yaml'));
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

    public function getPageData($pageid):PageData{
        $page_dir = $this->getPageDir();
        $global = $this->global_settings;

        if(!$this->pageExists($pageid)){
            return null;
        }

        $page = PageData::deserialize(Yaml::parseFile($page_dir.$pageid.'.yaml'));
        if(is_null($page)){
            return null;
        }

        if(is_null($page->getFavicon())){
            $page->setFavicon($global->getFavicon());
        }  

        foreach($global->getBlocks() as $block_name => $block){
            if(is_null($page->getBlock($block_name))){
                $page->addBlock($block_name, $block);
            }
        }

        return $page;
    }

    public function setBlock(string $page_id, string $block_id, BlockData $block){
        if(!$this->pageExists($page_id)){
            return false;
        }


    }    

}
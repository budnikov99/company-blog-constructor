<?php
namespace App\content;

use App\services\PageManager;

class StaticContent extends Content {
    private $html = null;

    protected function loadData(array $args){
        if($args['file']){
            $file = PageManager::getPageDir().$args['file'];

            if(file_exists($file)){
                $this->html = file_get_contents($file);
            }
        }
    }

    public function getHtml(){
        return $this->html;
    }
}
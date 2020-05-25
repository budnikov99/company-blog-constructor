<?php
namespace App\content;

use App\services\PageManager;

class StaticContent extends Content {
    private $html = null;

    protected function loadData(array $args){
        if(array_key_exists('content', $args)){
            $this->html = $args['content'];
            return true;
        }

        return false;
    }

    public function getHtml(){
        return $this->html;
    }
}
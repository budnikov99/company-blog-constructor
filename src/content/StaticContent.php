<?php
namespace App\content;

use App\Services\PageManager;

class StaticContent extends Content {
    private $html = null;

    protected function loadData(array $args, array $managers, array $get = []){
        $this->html = $args['content']??'';

        return true;
    }

    public function getHtml(){
        return $this->html;
    }
}
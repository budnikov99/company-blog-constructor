<?php
namespace App\content;

use App\services\PageManager;

class VoidContent extends Content {
    protected function loadData(array $args){
        return true;
    }
}
<?php
namespace App\services\data;

use App\content\Content;

class PageData {
    private $title = '';
    private $favicon = null;
    private $blocks = [];

    private $content = null;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function setFavicon($favicon){
        $this->favicon = $favicon;
    }

    public function getFavicon(){
        return $this->favicon;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getBlocks(){
        return $this->blocks;
    }

    public function getBlock($name){
        if(array_key_exists($name, $this->blocks)){
            return $this->blocks[$name];
        }else{
            return null;
        }
    }

    public function addBlock(BlockData $block){
        $this->blocks[$block->getName()] = $block;
    }

    public function setBlockList(array $blocks){
        $this->blocks = $blocks;
    }

    
    public function setContent(Content $content){
        $this->content = $content;
    }

    public function getContent(){
        return $this->content;
    }    

}
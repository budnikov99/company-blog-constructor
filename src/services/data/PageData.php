<?php
namespace App\services\data;

use App\content\Content;

class PageData extends Data {

    private $title = '';
    private $favicon = null;
    private $blocks = [];

    private $page_content_type = 'void';
    private $page_content = [];

    private $content = null;

    public function __construct(string $title, string $content_type)
    {
        $this->title = $title;
        $this->page_content_type = $content_type;
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

    public function setBlock(string $id, BlockData $block){
        $this->blocks[$id] = $block;
    }

    public function removeBlock(string $id){
        if(array_key_exists($id, $this->blocks)){
            unset($this->blocks[$id]);
        }
    }

    public function setBlockList(array $blocks){
        $this->blocks = $blocks;
    }

    
    public function setContent(Content $content){
        $this->content = $content;
    }

    public function createContentFromArgs(){
        $this->setContent(Content::create($this->getContentType(), $this->getContentArgs()));
    }

    public function getContent(){
        return $this->content;
    }    

    public function getContentType(){
        return $this->page_content_type;
    }

    public function getContentArgs(){
        return $this->page_content;
    } 

    public function setContentArgs(array $args){
        $this->page_content = $args;
    }

    protected static function deserialize_raw(array $data){
        Data::assertValueType($data['title'], 'string');
        if(array_key_exists('favicon', $data)){
            Data::assertValueType($data['favicon'], 'string');
        }
        Data::assertValueType($data['page_content'], 'array');
        Data::assertValueType($data['page_content']['type'], 'string');

        $page = new PageData($data['title'], $data['page_content']['type']);
        $page->setFavicon($data['favicon'] ?? null);
        $page->setContentArgs($data['page_content']);

        if(is_array($data['blocks'])){
            foreach($data['blocks'] as $block_name => $block_data){
                $block = BlockData::deserialize($block_data);
                if(is_null($block)){
                    return null;
                }else{
                    $page->setBlock($block_name, $block);
                }
            }
        }
        return $page;
    }

    public function serialize(){
        $page_content = $this->page_content;
        $page_content['type'] = $this->page_content_type;

        $data = [
            'title' => $this->title ?? '',
            'page_content' => $page_content,
            'blocks' => []
        ];

        if(!is_null($this->favicon)){
            $data['favicon'] = $this->favicon;
        }

        foreach($this->blocks as $block_name => $block){
            $data['blocks'][$block_name] = $block->serialize();
        }
        return $data;
    }

}
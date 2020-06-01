<?php
namespace App\content;

class PostContent extends Content {
    private $id = 'err';
    private $title = 'Пост';
    private $created = '01.01.1970';
    private $content = 'Содержимое поста';
    private $preview = 'Превью';
    private $category = 'Категория';
    protected function loadData(array $args){
        try {
            $this->id = $args['id'];
            $this->title = $args['title'];
            $this->created = $args['created'];
            $this->content = $args['content'];
            $this->preview = $args['preview'];
            $this->category = $args['category'];
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function getId(){
        return $this->id;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getCreated(){
        return $this->created;
    }

    public function getContent(){
        return $this->content;
    }

    public function getPreview(){
        return $this->preview;
    }

    public function getCategory(){
        return $this->category;
    }
}
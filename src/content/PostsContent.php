<?php
namespace App\content;

use App\Services\PostManager;

class PostsContent extends Content {
    private $categories = [];
    private $keywords = [];
    private $date_start = null;
    private $date_end = null;
    private $order = false;
    private $page_size = 69;
    private $amount = 0;

    private $posts = [];
    private $pages = 0;
    private $curr_page = 1;
    private $count = 0;
    private $offset = 0;

    protected function loadData(array $args, array $managers, array $get = []){
        $page = $get['page'] ?? 1;
        if($page < 1){
            $page = 1;
        }

        $this->categories = $args['categories']??[];
        $this->keywords = $args['keywords']??[];

        $this->date_start = ($args['date_start']??null)?:null;
        $this->date_end = ($args['date_end']??null)?:null;

        $this->order = $args['order']??false;
        $this->page_size = intval($args['page_size']??'30');
        $this->amount = intval($args['amount']??'0');

        $count = $managers[PostManager::class]->countPosts($this->categories, $this->date_start, $this->date_end, $this->keywords);

        $post_offset = ($page-1) * $this->page_size;

        if($post_offset >= $count){
            $page = intdiv($count, $this->page_size);
            $post_offset = ($page-1) * $this->page_size;
        }

        $this->offset = $post_offset;

        $this->count = $count;
        $this->pages = intdiv($count, $this->page_size);
        $this->curr_page = $page;
        if($count % $this->page_size != 0){
            $this->pages += 1;
        }

        $posts = $managers[PostManager::class]->loadPosts($post_offset, $this->page_size, $this->categories, $this->date_start, $this->date_end, $this->keywords, $this->order);
        foreach($posts as $post){
            $this->posts []= $post->serialize();
        }

        return true;
    }

    public function getCategories(){
        return $this->categories;
    }

    public function getKeywords(){
        return $this->keywords;
    }

    public function getDateStart(){
        return $this->date_start;
    }

    public function getDateEnd(){
        return $this->date_end;
    }

    public function getOrder(){
        return $this->order;
    }

    public function getPageSize(){
        return $this->page_size;
    }

    public function getLoadAmount(){
        return $this->amount;
    }

    public function getPosts(){
        return $this->posts;
    }

    public function getTotalPostCount(){
        return $this->count;
    }

    public function getPages(){
        return $this->pages;
    }
}
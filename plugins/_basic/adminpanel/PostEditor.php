<?php
namespace Plugins\_basic\adminpanel;

use App\Plugins\AdminPanelExtension;
use App\Services\PostManager;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostEditor extends AdminPanelExtension {
    private function getPostId(string $subpath){
        $parts = explode('/', $subpath);
        if(count($parts) == 2 && $parts[0] == 'post'){
            $id = intval($parts[1]);
            if(strval($id) == $parts[1]){
                return $id;
            }
        }
        return null;
    }

    private function getMode(string $subpath){
        if(empty($subpath)){
            $subpath = 'create';
        }
        $parts = explode('/', $subpath);
        $mode = $parts[0];

        if(is_null($this->getPostId($subpath)) && $mode == 'post'){
            $mode = 'posts';
        }

        switch($mode){
            case 'create':
            case 'posts':
            case 'categories':
            case 'post': return $mode; break;
            default: return null;
        }
    }

    public function getSubmenu(string $subpath, Request $request){
        $mode = $this->getMode($subpath)??'create';
        $data = [
            'active' => $mode,
            'items' => [
                'create' => 'Создать',
                'post' => 'Редактировать',
                'posts' => 'Список статей',
                'categories' => 'Категории',
            ],
        ];
        return $data;
    }

    public function getTemplateData(string $subpath, Request $request){
        if(!$subpath){
            $subpath = 'create';
        }
        $mode = $this->getMode($subpath);
        $id = $this->getPostId($subpath);
        $data = [];
        $template = '';

        if(!$mode){
            throw new NotFoundHttpException();
        }

        if($mode == 'posts'){
            $template = '_basic/templates/post-editor-posts.html.twig';
            $offset = $request->query->get('offset', 0);
            $limit = 15;
            $category = $request->query->get('category', null);
            $begin_date = $request->query->get('from_date', null);
            $end_date = $request->query->get('to_date', null);   

            if($begin_date && $end_date){
                try {
                    $begin_date = new DateTime($begin_date);
                    $end_date = new DateTime($end_date);
                } catch (\Throwable $th) {
                    $begin_date = null;
                    $end_date = null;
                }
            }else{
                $begin_date = null;
                $end_date = null;
            }
            $data['count'] = $this->managers[PostManager::class]->countPosts($category, $begin_date, $end_date);
            if($offset < 0 || $offset >= $data['count']){
                $offset = 0;
            }
            $data['posts'] = $this->managers[PostManager::class]->loadPosts($offset, $limit, $category, $begin_date, $end_date);
            $data['offset'] = $offset;
            $data['limit'] = $limit;
            $data['category'] = $category;
            $data['category_title'] = null;
            if($category){
                $cat =  $this->managers[PostManager::class]->getCategory($category);
                if($cat){
                    $data['category_title'] = $cat->getTitle();
                }
            }
            $data['from_date'] = null;
            $data['to_date'] = null;
            if(!is_null($begin_date)){
                $data['from_date'] = $begin_date->format('Y-m-d');
            }
            if(!is_null($end_date)){
                $data['to_date'] = $end_date->format('Y-m-d');
            }

        }else if($mode == 'post' || $mode == 'create'){
            $template = '_basic/templates/post-editor-edit.html.twig';

            $post = null;
            if(!is_null($id)){
                $post = $this->managers[PostManager::class]->getPost($id);
            }

            if(is_null($post)){
                $mode = 'create';
            }

            $data['post'] = $post;
            if($post){
                $data['post_json'] = json_encode($post->serialize());
            }else{
                $data['post_json'] = json_encode([
                    'title' => null,
                    'content' => null,
                    'preview' => null,
                    'category' => null,
                ]);
            }
        }
        
        $data['categories'] = $this->managers[PostManager::class]->loadCategories();

        if($mode == 'categories'){
            $template = '_basic/templates/post-editor-categories.html.twig';

            $cat_counts = [];
            foreach($data['categories'] as $cat){
                $cat_counts[$cat->getName()] = $this->managers[PostManager::class]->countPosts($cat->getName(), null, null);
            }

            $data['category_counts'] = $cat_counts;
        }

        
        $data['mode'] = $mode;
        return [
            'active' => $mode,
            'template' => $template,
            'data' => $data
        ];
    }
}
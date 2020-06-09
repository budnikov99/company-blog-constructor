<?php
namespace Plugins\_basic\controllers;

use App\Services\PostManager;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends AbstractController {
    public function savePost(PostManager $pm, Request $request, $id){
        $this->denyAccessUnlessGranted('ROLE_PUBLISHER');
        
        try {
            $data = json_decode($request->getContent(), true);
            $post = $pm->getPost($id);

            if(is_null($post)){
                $post = $pm->createPost($data['title'], $data['content'], $data['preview'], $data['image']??'', $data['category']);
                if(is_null($post)){
                    return new JsonResponse(['success' => false, 'message' => 'Не удалось создать пост'], Response::HTTP_INTERNAL_SERVER_ERROR);
                }
                return new JsonResponse(['success' => true, 'message' => '', 'post_id' => $post->getId()]);
            }else{
                $category = $pm->getCategory($data['category']);

                if(is_null($category)){
                    return new JsonResponse(['success' => false, 'message' => 'Категория не существует'], Response::HTTP_BAD_REQUEST);
                }

                $post->setTitle($data['title']);
                $post->setContent($data['content']);
                $post->setPreview($data['preview']);
                $post->setImage($data['image']??'');
                $post->setCategory($category);
                $pm->flushDB();
            }
        } catch (\Throwable $th) {
            return new JsonResponse(['success' => false, 'message' => 'Произошла ошибка', 'exception' => $th->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['success' => true, 'message' => '']);
    }

    public function removePost(PostManager $pm, $id){
        $this->denyAccessUnlessGranted('ROLE_PUBLISHER');
        
        if(!$pm->removePost($id)){
            return new JsonResponse(['success' => false, 'message' => 'Не удалось удалить статью'], Response::HTTP_BAD_REQUEST);
        }

        return new JsonResponse(['success' => true, 'message' => '']);
    }

    public function loadPosts(PostManager $pm, Request $request){
        $this->denyAccessUnlessGranted('ROLE_PUBLISHER');

        $first = $request->query->get('first', 0);
        $limit = $request->query->get('limit', 0);
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
       
        $list = $pm->loadPosts($first, $limit, $category, $begin_date, $end_date);
        $serialized = [];
        foreach($list as $post){
            $serialized []= $post->serialize();
        }

        return new JsonResponse($serialized);
    }   


    public function saveCategory(PostManager $pm, Request $request, $name){
        $this->denyAccessUnlessGranted('ROLE_PUBLISHER');

        $title = $request->query->get('title', null);
        if(!$title){
            return new JsonResponse(['success' => false, 'message' => 'Не указано название категории'], Response::HTTP_BAD_REQUEST);
        }
        if(!$pm->saveCategory($name, $title)){
            return new JsonResponse(['success' => false, 'message' => 'Не удалось создать категорию'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse(['success' => true, 'message' => '']);
    }

    public function removeCategory(PostManager $pm, $name){
        $this->denyAccessUnlessGranted('ROLE_PUBLISHER');

        try {
            if(!$pm->removeCategory($name)){
                return new JsonResponse(['success' => false, 'message' => 'Не удалось удалить категорию'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } catch (\Throwable $th) {
            return new JsonResponse(['success' => false, 'message' => 'Не удалось удалить категорию'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return new JsonResponse(['success' => true, 'message' => '']);
    }

}
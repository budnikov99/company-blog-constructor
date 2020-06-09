<?php
namespace Plugins\_basic\public_controllers;

use App\Services\PostManager;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostListController extends AbstractController {
    
    public function loadPosts(PostManager $pm, Request $request){
        
        try {
            $data = json_decode($request->getContent(), true);
            if(empty($data)){
                return new JsonResponse(['success' => false, 'message' => 'Переданные данные не являются объектов JSON']);
            }

            $offset = $data['offset'];
            $limit = $data['limit'];
            if($limit > 50){
                $limit = 50;
            }
            $categories = $data['categories'];
            $date_start = $data['date_start']??null;
            $date_end = $data['date_end']??null;
            if(!empty($date_start) && !empty($date_end)){
                $date_start = new DateTime($date_start);
                $date_end = new DateTime($date_end);
            }else{
                $date_start = null;
                $date_end = null;
            }
            $keywords = $data['keywords'];
            $order = $data['order'];

            $posts = $pm->loadPosts($offset, $limit, $categories, $date_start, $date_end, $keywords, $order);
            $list = [];
            foreach($posts as $post){
                $list []= $post->serialize();
            }

            return new JsonResponse(['success' => true, 'message' => '', 'data' => $list]);
            
        } catch (\Throwable $th) {
            return new JsonResponse(['success' => false, 'message' => 'Произошла ошибка', 'exception' => $th->getMessage()]);
        }
    }
}
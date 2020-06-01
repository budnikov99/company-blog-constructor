<?php
namespace App\Services;

use App\Entity\Post;
use App\Entity\PostCategory;
use App\Repository\PostCategoryRepository;
use App\Repository\PostRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

class PostManager extends Manager {
    public static $UNCATEGORIZED_NAME = 'uncategorized';

    private $entitym = null;
    private $postr = null;
    private $postcatr = null;

    public function __construct(EntityManagerInterface $entitym, PostRepository $postr, PostCategoryRepository $postcatr){
        $this->entitym = $entitym;

        $this->postr = $postr;
        $this->postcatr = $postcatr;        
    }

    public function createPost(string $title, string $content, string $preview = null, string $category = null){
        if(is_null($category)){
            $category = $this->postcatr->findOneBy(['name' => PostManager::$UNCATEGORIZED_NAME]);
            
        }else{
            if(gettype($category) == 'string'){
                $category = $this->postcatr->findOneBy(['name' => $category]);
            }
        }
        if(is_null($category)){
            return null;
        }

        $post = new Post();
        $post->setTitle($title);
        $post->setContent($content);
        $post->setCategory($category);

        $post->setCreationDate(new DateTime());
        $post->setPreview($preview);

        $this->entitym->persist($post);
        $this->entitym->flush();
        return $post;
    }

    public function createCategory(string $name){
        $cat = new PostCategory();
        $cat->setName($name);

        $this->entitym->persist($cat);
        $this->entitym->flush();
        return $cat;
    }

    private function createUncategorizedCategory(){
        $uncat = new PostCategory();
        $uncat->setName(PostManager::$UNCATEGORIZED_NAME);
        $uncat->setTitle('Без категории');
        return $uncat;
    }


    public function loadPosts(int $start_from = -1, int $limit = 0, array $catlist = null, DateTimeInterface $start = null, DateTimeInterface $end = null){
        return $this->postr->findByCategoriesAndDate($start_from, $limit, $catlist, $start, $end);
    }

    public function loadPost($id){
        return $this->postr->find($id);
    }

    public function initializeDB()
    {
        try {
            $this->entitym->persist($this->createUncategorizedCategory());
            $this->entitym->flush();
        } catch (\Throwable $th) {
            return false;
        }
        return true;
    }
}
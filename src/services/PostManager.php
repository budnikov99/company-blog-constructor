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

    public function saveCategory(string $name, string $title){
        $cat = $this->getCategory($name);
        if(is_null($cat)){
            $cat = new PostCategory();
        }
        $cat->setName($name);
        $cat->setTitle($title);

        $this->entitym->persist($cat);
        $this->entitym->flush();
        return $cat;
    }

    public function getCategory(string $name) {
        return $this->postcatr->find($name);
    }

    public function loadCategories(){
        return $this->postcatr->findAll();
    }

    public function removeCategory(string $name){
        if($name == PostManager::$UNCATEGORIZED_NAME){
            return false;
        }
        $cat = $this->getCategory($name);
        if($cat){
            $this->entitym->remove($cat);
            $this->entitym->flush();
            return true;
        }
        return false;
    }

    private function createUncategorizedCategory(){
        $uncat = new PostCategory();
        $uncat->setName(PostManager::$UNCATEGORIZED_NAME);
        $uncat->setTitle('Без категории');
        return $uncat;
    }

    public function createPost(string $title, string $content, string $preview = null, string $image = null, string $category = null){
        if(is_null($category)){
            $category = $this->getCategory(PostManager::$UNCATEGORIZED_NAME);
            
        }else{
            if(gettype($category) == 'string'){
                $category = $this->getCategory($category);
            }
        }
        if(is_null($category) || get_class($category) != PostCategory::class){
            return null;
        }

        $post = new Post();
        $post->setTitle($title);
        $post->setContent($content);
        $post->setCategory($category);

        $post->setCreationDate(new DateTime());
        $post->setPreview($preview);
        $post->setImage($image);

        $this->entitym->persist($post);
        $this->entitym->flush();
        return $post;
    }

    public function loadPosts(int $start_from = 0, int $limit = 0, $catlist = null, DateTimeInterface $begin = null, DateTimeInterface $end = null, array $keywords = null, bool $desc_order = false){
        return $this->postr->findByCategoriesAndDate($start_from, $limit, $catlist, $begin, $end, $keywords, $desc_order);
    }

    public function countPosts($catlist = null, DateTimeInterface $begin = null, DateTimeInterface $end = null, array $keywords = null){
        return $this->postr->countByCategoriesAndDate($catlist, $begin, $end, $keywords);
    }

    public function getPost($id){
        return $this->postr->find($id);
    }

    public function removePost($id){
        $post = $this->getPost($id);
        if($post){
            $this->entitym->remove($post);
            $this->entitym->flush();
            return true;
        }
        return false;
    }

    public function flushDB(){
        $this->entitym->flush();
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
<?php
namespace App\Controller;

use App\Services\AdminPanelRenderer;
use App\Services\PageRenderer;
use App\Services\PostManager;
use App\Services\SiteManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    private $page_generator = null;

    public function __construct(PageRenderer $pg)
    {
        $this->page_generator = $pg;
    }

    private function loadPage(string $pageid){
        $page = $this->page_generator->getPage($pageid);
        if(is_null($page)){
            throw $this->createNotFoundException('Страницы не существует.');
        }
        return new Response($page);
    }

    public function getPage($pageid){
        if($pageid[0] == '_'){
            //Страницы, id которых начинается с "_" должны обрабатываться отдельно.
            throw new NotFoundHttpException();
        }

        return $this->loadPage($pageid);
    }

    public function index(){
        return $this->loadPage('index');
    }

    public function getPost(PageRenderer $pr, $category, $id){
        return new Response($pr->getPost($category, $id));
    }

    public function search(PageRenderer $pr, Request $request){
        $keywords = $request->query->get('q');

        if($keywords){
            $keywords = explode(' ', $keywords);
        }

        return new Response($pr->searchPage($keywords));
    }

    public function test(PostManager $pm, EntityManagerInterface $em, SiteManager $sm){
        
        
        return new Response();
    }
}
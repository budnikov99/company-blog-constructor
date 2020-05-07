<?php
namespace App\Controller;

use App\services\PageRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    private $page_generator = null;

    public function __construct(PageRenderer $pg)
    {
        $this->page_generator = $pg;
    }

    public function getPage($pageid)
    {
        $page = $this->page_generator->getPage($pageid);
        if(is_null($page)){
            throw $this->createNotFoundException('Страницы не существует.');
        }
        return new Response($page);
    }

    public function index(){
        return $this->getPage('index');
    }
}
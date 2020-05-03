<?php
namespace App\Controller;

use App\services\PageRenderer;
use Symfony\Component\HttpFoundation\Response;

class MainController 
{
    private $page_generator = null;

    public function __construct(PageRenderer $pg)
    {
        $this->page_generator = $pg;
    }

    public function getPage($pageid)
    {
        $page = $this->page_generator->getPage($pageid);
        return new Response($page);
    }

    public function index(){
        return $this->getPage('index');
    }
}
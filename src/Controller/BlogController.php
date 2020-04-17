<?php
namespace App\Controller;

use App\services\PageGenerator;
use Symfony\Component\HttpFoundation\Response;

class BlogController 
{
    public function index(PageGenerator $pg)
    {
        $page = $pg->getPage();
        return new Response($page);
    }
}
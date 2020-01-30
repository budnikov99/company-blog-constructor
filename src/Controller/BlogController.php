<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class BlogController
{
    public function index()
    {
        return new Response(
            '<html><body>Hello world!</body></html>'
        );
    }
}
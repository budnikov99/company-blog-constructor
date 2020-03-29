<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class BlogController 
{
    public function index()
    {
        return new Response(
            '<html><head>
            <link rel=\'stylesheet\' href=\'theme/style/dummy.css\'>
            <script src=\'theme/script/dummy.js\'></script>
            </head><body>
            Hello world! '.SERVER_ROOT.'
            </body></html>'
        );
    }
}
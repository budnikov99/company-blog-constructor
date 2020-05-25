<?php
namespace App\Controller;

use App\services\AdminPanelRenderer;
use App\services\PageRenderer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MainController extends AbstractController
{
    private $page_generator = null;

    public function __construct(PageRenderer $pg)
    {
        $this->page_generator = $pg;
    }

    public function getPage($pageid)
    {
        if($pageid[0] == '_' && !true){
            //Страницы, id которых начинается с "_" должны обрабатываться отдельно.
            throw new NotFoundHttpException();
        }

        $page = $this->page_generator->getPage($pageid);
        if(is_null($page)){
            throw $this->createNotFoundException('Страницы не существует.');
        }
        return new Response($page);
    }

    public function index(){
        $page = $this->page_generator->getIndexPage();
        if(is_null($page)){
            throw $this->createNotFoundException('Страницы не существует.');
        }
        return new Response($page);
    }

    public function getAdminPanel(AdminPanelRenderer $admin, $path){
        return new Response($admin->render($path));
    }

    public function getAdminPanelMain(AdminPanelRenderer $admin){
        return $this->getAdminPanel($admin, null);
    }

    public function adminLogin(Request $request){
        return new RedirectResponse('/admin');
    }

    public function adminLogout(){
        return new RedirectResponse('/');
    }
}
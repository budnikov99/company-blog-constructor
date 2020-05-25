<?php
namespace App\services;

use App\services\data\ModuleData;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AdminPanelRenderer {
    private $themem = null;
    private $pluginm = null;
    private $pagem = null;
    private $twig = null;

    public function __construct(Environment $twig, ThemeManager $themem, PluginManager $pluginm, PageManager $pagem){
        $this->themem = $themem;
        $this->pluginm = $pluginm;
        $this->pagem = $pagem;
        $this->twig = $twig;

        $loader = $this->twig->getLoader();
        if($loader instanceof FilesystemLoader){
            $loader->addPath(SERVER_ROOT.'/plugins');
        }
    }


    public function render($path){
        $data = [
            'active_extension' => '',
            'content_html' => '',
            'active_submenu_item' => '',
            'active_submenu_items' => [],
            'menu_items' => [],
        ];
        foreach($this->pluginm->getExtensionList() as $name => $extension){
            $data['menu_items'][$name] = $extension->getTitle();
        }
        if(!empty($path)){
            $subpath = explode('/', $path);
            if(count($subpath) < 2){
                throw new NotFoundHttpException();
            }

            $plugin = $subpath[0];
            $extension_name = $subpath[1];
            $extension_id = $plugin.'/'.$extension_name;
            $subpath = (count($subpath) > 2)?implode('/', array_slice($subpath, 2)):'';

            if(!is_null($this->pluginm->getExtension($extension_id))){
                $extension = $this->pluginm->getExtension($extension_id);
                $data['active_extension'] = $extension_id;

                $submenu = $extension->getSubmenu($subpath);
                $data['active_submenu_items'] = $submenu['items'];
                $data['active_submenu_item'] = $submenu['active'];

                $ext_data = $extension->getTemplateData($subpath);
                if(!empty($ext_data)){
                    $data['content_html'] = $this->twig->render($ext_data['template'], ['data' => $ext_data['data']]);
                }
            }
        }

        return $this->twig->render('twig_util/adminpanel.html.twig', ['data' => $data]);
    }
}
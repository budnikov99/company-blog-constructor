<?php
namespace App\Services;

use App\Services\Data\ModuleData;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AdminPanelRenderer {
    private $pluginm = null;
    private $twig = null;
    private $sitem = null;
    private $security = null;
    private $authu = null;

    public function __construct(Environment $twig, SiteManager $sitem, PluginManager $pluginm, Security $security, AuthenticationUtils $authu){
        $this->pluginm = $pluginm;
        $this->twig = $twig;
        $this->sitem = $sitem;
        $this->security = $security;
        $this->authu = $authu;

        $loader = $this->twig->getLoader();
        if($loader instanceof FilesystemLoader){
            $loader->addPath(SERVER_ROOT.'/plugins');
        }
    }


    public function render($path, Request $request){
        if(!$this->sitem->isInstalled()){
            return $this->sitem->renderInstaller();
        }

        if(!$this->security->isGranted('IS_AUTHENTICATED_REMEMBERED')){
            $error = $this->authu->getLastAuthenticationError();
            return $this->twig->render('twig_util/adminpanel-login.html.twig', [
                'success' => is_null($error), 
                'error' => $error, 
                'username' => $this->authu->getLastUsername(),
            ]);
        }
        $data = [
            'username' => $this->security->getUser()->getUsername(),
            'active_extension' => '',
            'active_extension_name' => '',
            'content_html' => '',
            'active_submenu_item' => '',
            'active_submenu_items' => [],
            'menu_items' => [],
        ];

        foreach($this->pluginm->getExtensionList() as $name => $extension){
            $allowed = true;
            foreach($extension->getRoles() as $role){
                if(!$this->security->isGranted($role)){
                    $allowed = false;
                    break;
                }
            }
            if($allowed){
                $data['menu_items'][$name] = $extension->getTitle();
            }
        }

        if(empty($path) && !empty($data['menu_items'])){
            $path = array_key_first($data['menu_items']);
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
                $allowed = true;
                foreach($extension->getRoles() as $role){
                    if(!$this->security->isGranted($role)){
                        throw new AccessDeniedHttpException();
                    }
                }
                $data['active_extension'] = $extension_id;
                $data['active_extension_name'] = $extension->getTitle();

                $submenu = $extension->getSubmenu($subpath, $request);
                $ext_data = $extension->getTemplateData($subpath, $request);
                $data['active_submenu_items'] = $submenu['items'];
                $data['active_submenu_item'] = $ext_data['active'] ?? $submenu['active'];

                
                $ext_urls = [
                    'panel_url' => '/admin/'.$extension_id,
                    'assets_url' => '/assets/plugin/'.$plugin,
                    'controller_url' => '/plugin/'.$plugin,
                    'public_controller_url' => '/public/plugin/'.$plugin,
                ];
                if(!empty($ext_data)){
                    $data['content_html'] = $this->twig->render($ext_data['template'], [
                        'data' => $ext_data['data'], 
                        'url' => $ext_urls,
                        ]);
                }
            }
        }

        return $this->twig->render('twig_util/adminpanel.html.twig', ['data' => $data]);
    }
}
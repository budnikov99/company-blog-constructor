<?php
namespace App\Services;

use App\content\Content;
use App\Services\Data\ModuleData;
use App\Services\Data\PageData;
use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;
use Psr\Log\LoggerInterface;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;

class PageRenderer {
    private $themem = null;
    private $postm = null;
    private $pagem = null;
    private $pluginm = null;
    private $twig = null;
    private $sitem = null;

    private $managers = [];

    public function __construct(Environment $twig, ThemeManager $themem, PluginManager $pluginm, PostManager $postm, PageManager $pagem, SiteManager $sitem, AssetManager $assetm){
        $this->themem = $themem;
        $this->postm = $postm;
        $this->pagem = $pagem;
        $this->sitem = $sitem;
        $this->pluginm = $pluginm;

        $this->managers = [
            AssetManager::class => $assetm,
            PageManager::class => $pagem,
            PluginManager::class => $pluginm,
            SiteManager::class => $sitem,
            ThemeManager::class => $themem,
            PostManager::class => $postm,
        ];

        $this->twig = $twig;
        $loader = $this->twig->getLoader();
        if($loader instanceof FilesystemLoader){
            $loader->addPath($themem->getThemeDir());
        }
        if($_SERVER['APP_DEBUG']){
            $this->twig->addExtension(new \Twig\Extension\DebugExtension());
        }
    }

    private function renderBlockData($page_data){
        $theme_data = $this->themem->getBlocks();
        
        foreach($page_data->getBlocks() as $name => $block){
            $block_theme = $theme_data[$name];
            $formats = $block_theme->getAcceptedFormats();

            if($block->getActive()){
                foreach($block->getModules() as $module){

                    if(is_null($this->pluginm->getModule($module->getName()))){
                        StaticLogger::error('???? ???????????????? ???????????????? ???????????????????????????? ????????????', [
                            'module' => $module->getName(),
                            'block' => $name
                        ]);
                        $block->setActive(false);
                        continue;
                    }

                    if(in_array($this->pluginm->getModule($module->getName())->getFormat(), $formats)){
                        $module->setData($this->pluginm->getModule($module->getName())->getData($module->getArgs()));
                    }else{
                        StaticLogger::error('???????? ???? ???????????????????????? ???????????? ???????????? ???????????? ', [
                            'block' => $name,
                            'module' => $module->getName(),
                            'format' => $this->pluginm->getModule($module->getName())->getFormat()
                        ]);
                    }
                }
            }
        }
    }

    public function getPageData($pageid){
        if(!$this->pagem->pageExists($pageid)){
            return null;
        }

        return $this->pagem->getPageData($pageid);
    }

    public function generateTemplateData(string $pageid, PageData $page_data){
        $this->renderBlockData($page_data);

        $data = [
            'page_id' => $pageid,
            'data' => $page_data,   
            'data_json' => json_encode($page_data->serialize()),
            'site_settings' => $this->sitem->getPublicSettingList(),
            'theme_settings' => $this->themem->getSettings(),         
        ];

        return $data;
    }

    public function getPage($pageid){
        if(!$this->sitem->isInstalled()){
            return $this->sitem->renderInstaller();
        }

        $page = $this->getPageData($pageid);
        if(is_null($page)){
            throw new NotFoundHttpException();
        }

        $page->createContentFromArgs($this->managers, Request::createFromGlobals()->query->all());
        if(is_null($page->getContent()) || !$page->getContent()->isLoaded()){
            throw new InternalErrorException('???????????? ?????? ???????????????? ????????????????');
        }
    
        return $this->renderPage($this->generateTemplateData($pageid, $page));
    }

    public function renderPage($data){
        $template_name = $this->themem->getMainTemplate();
        return $this->twig->render($template_name, $data);
    }

    public function getPost(string $category, $id){
        if(!$this->sitem->isInstalled()){
            return $this->sitem->renderInstaller();
        }

        $post = $this->postm->getPost($id);

        if(is_null($post) || $post->getCategory()->getName() != $category){
            throw new NotFoundHttpException();
        }

        $page = $this->getPageData('_post');
        $page->setTitle($post->getTitle());

        $page->setContentType('post');
        $page->setContentArgs($post->serialize());

        $page->createContentFromArgs($this->managers);

        $data = $this->generateTemplateData('_post', $page);
        return $this->renderPage($data);
    }

    public function searchPage(array $keywords){
        if(!$this->sitem->isInstalled()){
            return $this->sitem->renderInstaller();
        }

        if(!empty($keywords)){
            $tmp = [];
            foreach($keywords as $word){
                if(!empty($word)){
                    $tmp []= $word;
                }
            }
            $keywords = $tmp;
        }

        $page = $this->getPageData('_search');

        if(!empty($keywords)){
            $page->setContentType('posts');
            $page->setContentArgs([
                'keywords' => $keywords,
                'order' => true,
                'page_size' => 30,
            ]);
        }else{
            $page->setContentType('static');
            $page->setContentArgs([
                'content' => '',
            ]);
        }

        $page->createContentFromArgs($this->managers);

        $data = $this->generateTemplateData('_search', $page);
        return $this->renderPage($data);
    }

}

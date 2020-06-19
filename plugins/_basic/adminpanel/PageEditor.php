<?php
namespace Plugins\_basic\adminpanel;

use App\Plugins\AdminPanelExtension;
use App\Services\Data\ModuleData;
use App\Services\PageManager;
use App\Services\PluginManager;
use App\Services\PostManager;
use App\Services\ThemeManager;
use Symfony\Component\HttpFoundation\Request;

class PageEditor extends AdminPanelExtension {
    private function getPageId($subpath){
        if(substr($subpath, 0, 5) == 'page/'){
            return substr($subpath, 5);
        }
        return null;
    }

    private function isPageEditable($pageid){
        if(!empty($pageid) && $pageid != '_global' && $this->managers[PageManager::class]->pageExists($pageid)){
            return true;
        }else{
            return false;
        }
    }

    private function getMode($subpath){
        if($subpath == 'global'){
            return 'global';
        }
        $pageid = $this->getPageId($subpath);
        if(empty($subpath) || $subpath == 'create' || (!empty($pageid) && !$this->isPageEditable($pageid))){
            return 'create';
        }else if($this->isPageEditable($pageid)){
            return 'edit';
        }else if($subpath == 'list' || $subpath == 'page'){
            return 'list';
        }else{
            return '';
        }
    }

    public function getSubmenu(string $subpath, Request $request){
        $mode = $this->getMode($subpath);
        $data = [
            'active' => $mode,
            'items' => [
                'create' => 'Создать страницу',
                'global' => 'Общие настройки блоков',
                'page' => 'Редактировать страницу',
                'list' => 'Список страниц',
            ],
        ];

        return $data;
    }

    public function getTemplateData(string $subpath, Request $request){
        $pageid = $this->getPageId($subpath)??'';
        $mode = $this->getMode($subpath);

        if($mode == 'list'){
            $pages = [];
            foreach($this->managers[PageManager::class]->getPageList() as $name){
                if($name != '_global'){
                    $pages[$name] = $this->managers[PageManager::class]->getCorrectedPage($name);
                }
            }
            return [
                'template' => '_basic/templates/page-editor-list.html.twig',
                'data' => [
                    'pages' => $pages,
                ],
            ];
        }else if($mode != ''){
            $global_page = $this->managers[PageManager::class]->getGlobalPage();

            $modules_serialized = [];
            foreach($this->managers[PluginManager::class]->getModuleList() as $key => $value){
                $modules_serialized[$key] = ModuleData::createFromModule($value)->serialize();
            }

            $blocks_serialized = [];
            foreach($this->managers[ThemeManager::class]->getBlocks() as $key => $value){
                $blocks_serialized[$key] = $value->serialize();
            }

            $current_page = null;
            if(!empty($pageid) && $mode == 'edit'){
                $current_page = $this->managers[PageManager::class]->getCorrectedPage($pageid);
            }else{
                $current_page = $this->managers[PageManager::class]->getBlankPage();
            }

            return [
                'active' => $pageid?'page':$mode,
                'template' => '_basic/templates/page-editor.html.twig',
                'data' => [
                    'create_mode' => $mode == 'create',
                    'global_mode' => $mode == 'global',
                    'page_id' => $pageid,
                    'page_json' => json_encode($current_page->serialize()),
                    'global_json' => json_encode($global_page->serialize()),
                    'blockinfo_json' => json_encode($blocks_serialized),
                    'modules_json' => json_encode($modules_serialized),
                    'categories' => $this->managers[PostManager::class]->loadCategories(),
                ],
            ];
        }
        return null;
    }
}
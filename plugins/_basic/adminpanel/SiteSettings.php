<?php
namespace Plugins\_basic\adminpanel;

use App\Plugins\AdminPanelExtension;
use App\Services\Data\ModuleData;
use App\Services\PageManager;
use App\Services\PluginManager;
use App\Services\SiteManager;
use App\Services\ThemeManager;
use Symfony\Component\HttpFoundation\Request;

class SiteSettings extends AdminPanelExtension {
    public function getSubmenu(string $subpath, Request $request){
        $data = [
            'active' => '',
            'items' => [],
        ];
        return $data;
    }

    public function getTemplateData(string $subpath, Request $request){
        return [
        'template' => '_basic/templates/site-settings.html.twig',
        'data' => ['settings' => $this->managers[SiteManager::class]->getPublicSettingList()],
        ];
    }
}
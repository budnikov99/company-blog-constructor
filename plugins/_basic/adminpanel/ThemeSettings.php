<?php
namespace Plugins\_basic\adminpanel;

use App\Plugins\AdminPanelExtension;
use App\Services\Data\ModuleData;
use App\Services\PageManager;
use App\Services\PluginManager;
use App\Services\SiteManager;
use App\Services\ThemeManager;
use Symfony\Component\HttpFoundation\Request;

class ThemeSettings extends AdminPanelExtension {
    public function getSubmenu(string $subpath, Request $request){
        $data = [
            'active' => $subpath??'settings',
            'items' => [
                'settings' => 'Настройки темы',
                'change' => 'Сменить тему'
            ],
        ];
        return $data;
    }

    public function getTemplateData(string $subpath, Request $request){
        $mode = $subpath == 'change'?'change':'settings';
        $data = [
            'mode' => $mode,
        ];

        if($mode == 'change'){
            $themes = [];
            $list = $this->managers[ThemeManager::class]->getThemesList();
            foreach($list as $theme => $title){
                $themes[$theme] = $this->managers[ThemeManager::class]->getThemeDataByName($theme);
            }
            $data['themes'] = $themes;
            $data['active'] = $this->managers[ThemeManager::class]->getActiveTheme();
        }else{
            $data['active'] = $this->managers[ThemeManager::class]->getActiveTheme();
            $data['active_title'] = $this->managers[ThemeManager::class]->getThemesList()[$data['active']];
            $data['settings'] = $this->managers[ThemeManager::class]->getSettings();
            $data['settings_json'] = json_encode($data['settings']);
        }

        return [
            'active' => $mode,
            'template' => '_basic/templates/theme-settings.html.twig',
            'data' => $data,
        ];
    }
}
<?php
namespace Plugins\_basic\adminpanel;

use App\Plugins\AdminPanelExtension;
use App\Services\Data\ModuleData;
use App\Services\PageManager;
use App\Services\PluginManager;
use App\Services\ThemeManager;
use Symfony\Component\HttpFoundation\Request;

class FileManager extends AdminPanelExtension {
    public function getSubmenu(string $subpath, Request $request){
        $data = [
            'active' => '',
            'items' => [],
        ];
        return $data;
    }

    public function getTemplateData(string $subpath, Request $request){
        return [
        'template' => '_basic/templates/file-manager.html.twig',
        'data' => [],
        ];
    }
}
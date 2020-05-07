<?php
namespace App\services;

use Symfony\Component\Yaml\Yaml;

class SiteManager {
    private $themem = null;
    public function __construct(ThemeManager $themem){
        $this->themem = $themem;
    }

    public function resetPages(){
        if(!file_exists(SERVER_ROOT.'\\data\\pages')){
            mkdir(SERVER_ROOT.'\\data\\pages');
        }

        file_put_contents(SERVER_ROOT.'\\data\\pages\\index.yaml', Yaml::dump([
            'title' => 'Главная',
            'page_content' => [
                'type' => 'static',
                'file' => 'index.html'
            ],
            'blocks' => null
        ]));
        file_put_contents(SERVER_ROOT.'\\data\\pages\\index.html', '<h1>Это шаблон главной страницы. Используйте конструктор, чтобы изменить её.</h1>');
        file_put_contents(SERVER_ROOT.'\\data\\pages\\_post.yaml', Yaml::dump([
            'title' => 'Страница отображения публикаций',
            'page_content' => [
                'type' => 'post',
            ],
            'blocks' => null
        ]));

        $global_data = [
            'title' => 'Общие настройки страниц',
            'page_content' => [
                'type' => 'void',
            ],
            'blocks' => []
        ];

        $theme_data = $this->themem->getThemeData();
        foreach($theme_data['blocks'] as $block_name => $tmp){
            $global_data['blocks'][$block_name] = [
                'active' => false,
                'modules' => null
            ];
        }
        file_put_contents(SERVER_ROOT.'\\data\\pages\\_global.yaml', Yaml::dump($global_data));
    }    
}
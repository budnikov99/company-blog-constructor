<?php
namespace App\plugins;

use App\services\PluginManager;

abstract class AdminPanelExtension {
    private $name = 'plugin/extension';
    private $title = 'Расширение';
    protected $managers = [];

    public function __construct(string $plugin, string $name, string $title, array $managers){
        $this->name = $plugin.'/'.$name;
        $this->title = $title;
        $this->managers = $managers;
    }

    /**
     * Возвращает список элементов подменю админ панели
     *
     * @param string $subpath - строка, идущая за /admin/<плагин>/<название>/
     * @return array - ['active' => 'id', 'items' => ['id' => 'Подпись', 'id2' => 'Подпись']]
     */
    public abstract function getSubmenu(string $subpath);

    /**
     * Переопределяемая функция возвращает путь к шаблону произвольные данные, передаваемые в шаблон
     * Если получено null, шаблон отрисован не будет
     *
     * @param string $subpath - строка, идущая за /admin/<плагин>/<название>/
     * @return array|null - ['template' => 'путь к шаблону', 'data' => произвольный объект данных]
     */
    public abstract function getTemplateData(string $subpath);

    public function getName(){
        return $this->name;
    }

    public function getTitle(){
        return $this->title;
    }
}
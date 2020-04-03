<?php
namespace App\formats;

use App\formats\Format;

class FormFormat extends Format {
    private $inputs = array();
    private $action = '';
    private $title = '';

    /**
     * Добавляет новый инпут в форму.
     *
     * @param [type] $name - название инпута
     * @param [type] $type - атрибут type (text, password, button, ...)
     * @param [type] $title - подпись инпута, которая будет расположена рядом с ним, в зависимости от темы
     * @param [type] $value - атрибут value
     * @return void
     */
    public function addInput($name, $type, $title, $value){
        $this->inputs[] = array('name' => $name, 'type' => $type, 'title' => $title, 'value' => $value);
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function setAction($action){
        $this->action = $action;
    }

    protected function toData()
    {
        return array('action' => $this->action, 'title' => $this->title, 'inputs' => $this->inputs);
    }
}
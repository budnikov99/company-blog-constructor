<?php
namespace App\formats;

use App\formats\Format;

class FormFormat extends Format {
    public $inputs = array();
    public $action = '';
    public $title = '';
    public $enctype = 'application/x-www-form-urlencoded';

    /**
     * Добавляет элемент в форму
     *
     * @param string $name - идентификатор
     * @param string $type - тип (email, password, checkbox, ...)
     * @param string $title - подпись элемента
     * @param string $value - значение элемента
     * @param string $list - идентификатор списка datalist
     * @return void
     */
    public function addInput(string $name, string $type, string $title, string $value, string $list = ''){
        $this->inputs[] = array('tag' => 'input', 'name' => $name, 'type' => $type, 
                        'title' => $title, 'value' => $value, 'list' => $list);
    }

    /**
     * Добавляет в форму
     *
     * @param string $id - идентификатор для использования в $list элемента
     * @param array $options - массив значений
     * @return void
     */
    public function addDatalist(string $id, array $options){
        $this->inputs[] = array('tag' => 'datalist', 'id' => $id, 'options' => $options);
    }

    /**
     * Добавляет текстовую область
     *
     * @param string $name - идентификатор
     * @param string $title - подпись
     * @param integer $cols - ширина поля в символах
     * @param integer $rows - количество строк
     * @return void
     */
    public function addTextarea(string $name, string $title, $cols = 40, $rows = 3){
        $this->inputs[] = array('tag' => 'textarea', 'name' => $name, 'title' => $title, 'cols' => $cols, 'rows' => $rows);
    }

    public function setTitle($title){
        $this->title = $title;
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function setEnctype($enctype){
        $this->enctype = $enctype;
    }

}
<?php
namespace App\Plugins;

class ArgListItem {
    public $title = '';
    public $value = null;

    public function __construct(string $title, $value)
    {
        $this->title = $title;
        $this->value = $value;
    }
}
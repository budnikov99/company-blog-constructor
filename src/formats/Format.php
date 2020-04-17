<?php
namespace App\formats;

abstract class Format {
    public function getFormatName(){
        $name = explode('\\',get_class($this));
        $name = $name[count($name)-1];

        return $name;
    }

    public function getData(){
        $name = $this->getFormatName();

        return array('format' => $name, 'data' => $this);
    }
}

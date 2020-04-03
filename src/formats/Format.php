<?php
namespace App\formats;

abstract class Format {
    abstract protected function toData();

    public function getData(){
        $name = explode('\\',get_class($this));
        $name = $name[count($name)-1];

        return array('format' => $name, 'data' => $this->toData());
    }
}

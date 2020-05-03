<?php
namespace App\formats;

use App\formats\Format;

class ButtonFormat extends Format {
    public $href = '';
    public $text = '';
    public $img = null;

    public function setHref($href){
        $this->href = $href;
    }

    public function setText($text){
        $this->text = $text;
    }

    /**
     * Устанавливает картинку кнопки. Если картинка установлена, текст кнопки игнорируется.
     *
     * @param string $img - URL картинки
     * @return void
     */
    public function setImg(string $img){
        $this->img = $img;
    }

}
<?php
namespace App\formats;

use App\formats\Format;

class ErrorFormat extends Format {
    public $message;

    public function setMessage($message){
        $this->message = $message;
    }

}

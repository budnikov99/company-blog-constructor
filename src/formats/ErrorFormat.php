<?php
namespace App\formats;

use App\formats\Format;

class ErrorFormat extends Format {
    private $message;

    public function setMessage($message){
        $this->message = $message;
    }

    protected function toData()
    {
        return $this->message;
    }
}

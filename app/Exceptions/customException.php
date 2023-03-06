<?php
namespace App\Exceptions;

use Exception;

class CustomException extends Exception
{
    public $message;
    public $status;

    public function __construct($message, $status)
    {
        $this->message = $message;
        $this->status = $status;
    }
}

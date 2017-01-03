<?php

namespace SINSQL\Exceptions;


use Exception;

class SINQLException extends \Exception
{
    public function __construct($message = "")
    {
        $message = '[SINSQL PARSE ERROR] ' . $message;
        parent::__construct($message, 0, null);
    }
}
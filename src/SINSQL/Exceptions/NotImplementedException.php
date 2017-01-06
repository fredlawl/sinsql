<?php

namespace SINSQL\Exceptions;


class NotImplementedException extends SINQLException
{
    public function __construct()
    {
        $message = "Not Implemented";
        parent::__construct($message);
    }
}
<?php


namespace SINSQL\Exceptions;


class FailedToParseException extends SINQLException
{
    public function __construct($message, $lineColumn)
    {
        $message = sprintf(
            "%s near line %s",
            $message,
            $lineColumn
        );
        parent::__construct($message);
    }
}
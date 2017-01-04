<?php


namespace SINSQL\Exceptions;


class FailedToParseException extends SINQLException
{
    public function __construct($message, $lineNumber, $column)
    {
        $message = sprintf(
            "%s starting line %d:%d",
            $message,
            $lineNumber,
            $column
        );
        parent::__construct($message);
    }
}
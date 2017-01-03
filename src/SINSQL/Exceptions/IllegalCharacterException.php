<?php

namespace SINSQL\Exceptions;


class IllegalCharacterException extends SINQLException
{
    public function __construct($character, $lineNumber, $column)
    {
        $message = sprintf(
            "Illegal character '%s' on line %d:%d",
            $character,
            $lineNumber,
            $column
        );
        parent::__construct($message);
    }
}
<?php

namespace SINSQL\Exceptions;


class IllegalCharacterException extends SINQLException
{
    public function __construct($character, $lineColumn)
    {
        $message = sprintf(
            "Illegal character '%s' on line %s.",
            $character,
            $lineColumn
        );
        parent::__construct($message);
    }
}
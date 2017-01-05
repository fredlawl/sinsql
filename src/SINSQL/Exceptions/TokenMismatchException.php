<?php

namespace SINSQL\Exceptions;


use SINSQL\Token;

class TokenMismatchException extends SINQLException
{
    public function __construct($expected, $got, $lineColumn)
    {
        $expected = Token::stringify($expected);
        $got = Token::stringify($got);
        $message = sprintf(
            "Token mismatch. Expected %s, but got '%s' instead on line %s.",
            $expected,
            $got,
            $lineColumn
        );
        parent::__construct($message);
    }
}
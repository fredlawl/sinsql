<?php

namespace SINSQL\Exceptions;


use SINSQL\Token;

class TokenMismatchException extends SINQLException
{
    public function __construct($expected, $got)
    {
        $expected = Token::stringify($expected);
        $got = Token::stringify($got);
        $message = sprintf(
            "Token mismatch. Expected %s, but got %s.",
            $expected,
            $got
        );
        parent::__construct($message);
    }
}
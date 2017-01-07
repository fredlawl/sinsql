<?php

namespace SINSQL\Exceptions;


use SINSQL\Token;

class TokenMismatchException extends SINQLException
{
    public function __construct($expected, $got, $lineColumn)
    {
        if (is_array($expected)) {
            $expected = implode(', ', array_map(function ($token) {
                return Token::stringify($token);
            }, $expected));
        } else {
            $expected = Token::stringify($expected);
        }
        
        $got = Token::stringify($got);
        $message = sprintf(
            "Token mismatch. Expected '%s', but got '%s' instead on line %s.",
            $expected,
            $got,
            $lineColumn
        );
        parent::__construct($message);
    }
}
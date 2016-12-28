<?php

namespace SINSQL\Operations;


use SINSQL\Token;

class OperationRegistry
{
    private static $operatorRegistry = [
        Token::LOGCICIN => '\\SINSQL\\Operations\\InOperation',
        Token::LOGICAND => '\\SINSQL\\Operations\\AndOperation',
        Token::LOGICOR => '\\SINSQL\\Operations\\OrOperation',
        Token::LOGICNOT => '\\SINSQL\\Operations\\NotOperation',
        Token::LOGICEQ => '\\SINSQL\\Operations\\EqualOperation'
    ];
    
    public static function getOperation($token)
    {
        if (!isset(self::$operatorRegistry[$token]) || !class_exists(self::$operatorRegistry[$token]))
            throw new \Exception('Operation Registry has no entry for "' . $token . '"');
        
        return new self::$operatorRegistry[$token]();
    }
}
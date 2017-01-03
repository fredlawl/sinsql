<?php

namespace SINSQL;


class Token
{
    const TXT_LEFTPARAN = 0;
    const TXT_RIGHTPARAN = 1;
    
    const EXP_OR = 2;
    const EXP_AND = 3;
    const EXP_SEQUENCE = 4;
    const EXP_NOTSEQUENCE = 5;
    const EXP_EQUALS = 6;
    const EXP_NOTEQUALS = 7;
    const EXP_LESSTHAN = 8;
    const EXP_LESSTHANEQUALS = 9;
    const EXP_GREATERTHAN = 10;
    const EXP_GREATERTHANEQUALS = 11;
    const TXT_COLON = 12;
    const TXT_DOT = 13;
    const TXT_COMMA = 14;
    const TXT_QUOTE = 15;
    
    const TXT_NUMBER = 100;
    const TXT_CHARACTER = 1001;
    const TXT_STRING = 1002;
    const EOF = 9999;
    
    private static $tokenMap = null;
    private static $reverseTokenMap = null;
    
    public static function parseToken($token, &$out)
    {
        self::buildTokenMap();
        if (!isset(self::$tokenMap[$token]))
            return false;
        
        $out = self::$tokenMap[$token];
        return true;
    }
    
    public static function getToken($characters, &$out)
    {
        self::buildTokenMap();
        if (!isset(self::$reverseTokenMap[$characters]))
            return false;
        $out = self::$reverseTokenMap[$characters];
        return true;
    }
    
    private static function buildTokenMap()
    {
        if (!(is_null(self::$tokenMap) || is_null(self::$reverseTokenMap)))
            return;
        
        self::$tokenMap = [
            self::TXT_LEFTPARAN => '(',
            self::TXT_RIGHTPARAN => ')',
            self::EXP_OR => 'OR',
            self::EXP_AND => 'AND',
            self::EXP_SEQUENCE => 'IN',
            self::EXP_NOTSEQUENCE => 'NOT IN',
            self::EXP_EQUALS => 'IS',
            self::EXP_NOTEQUALS => 'IS NOT',
            self::EXP_LESSTHAN => 'LESS THAN',
            self::EXP_LESSTHANEQUALS => 'LESS THAN OR IS',
            self::EXP_GREATERTHAN => 'GREATER THAN',
            self::EXP_GREATERTHANEQUALS => 'GREATER THAN OR IS',
            self::TXT_COLON => ':',
            self::TXT_DOT => '.',
            self::TXT_COMMA => ',',
            self::TXT_QUOTE => '"'
        ];
        
        self::$reverseTokenMap = array_flip(self::$tokenMap);
    }
}
<?php

namespace SINSQL;


class Token
{
    const TXT_LEFTPAREN = '(';
    const TXT_RIGHTPAREN = ')';
    
    const TXT_COLON = ':';
    const TXT_DOT = '.';
    const TXT_COMMA = ',';
    const TXT_QUOTE = '"';
    const TXT_SPACE = ' ';
    
    const TXT_NUMBER = 100;
    const TXT_CHARACTER = 1001;
    const TXT_STRING = 1002;
    const TXT_SYMBOL = 1003;
    
    const EOF = 9999;
    
    private static $tokenNamesTable = null;
    private static $tokenValueLookupTable = null;
    
    public static function getToken($characters, &$out)
    {
        self::buildTokenMap();
        if (!isset(self::$tokenNamesTable[$characters]))
            return false;
        
        $out = constant(__CLASS__ . '::' . self::$tokenNamesTable[$characters]);
        return true;
    }
    
    public static function parseToken($token, &$out)
    {
        self::buildTokenMap();
        $token = self::stringify($token);
        if (!isset(self::$tokenValueLookupTable[$token]))
            return false;
        
        $out = self::$tokenValueLookupTable[$token];
        return true;
    }
    
    private static function buildTokenMap()
    {
        if (!(is_null(self::$tokenNamesTable) || is_null(self::$tokenValueLookupTable)))
            return;
    
        $class = new \ReflectionClass(__CLASS__);
        self::$tokenValueLookupTable = $class->getConstants();
        self::$tokenNamesTable = array_flip(self::$tokenValueLookupTable);
    }
    
    public static function stringify($token)
    {
        self::buildTokenMap();
        
        if (!isset(self::$tokenNamesTable[$token]))
            return null;
        
        return self::$tokenNamesTable[$token];
    }
}
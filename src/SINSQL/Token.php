<?php

namespace SINSQL;


class Token
{
    const TOK_LEFTPAREN = '(';
    const TOK_RIGHTPAREN = ')';
    
    const TOK_LEFTBRACK = '[';
    const TOK_RIGHTBRACK = ']';
    
    const TOK_COLON = ':';
    const TOK_DOT = '.';
    const TOK_COMMA = ',';
    const TOK_QUOTE = '"';
    const TOK_SPACE = ' ';
    
    const TOK_NUMBER = 1000;
    const TOK_CHARACTER = 1001;
    const TOK_STRING = 1002;
    const TOK_SYMBOL = 1003;
	const TOK_VARIABLE = 1004;
    
    const TOK_EOF = 9999;
    
    private static $tokenNamesTable = null;
    private static $tokenValueLookupTable = null;
    private static $tokenConstantLookupTable = [];
    
    public static function getToken($characters, &$out)
    {
        self::buildTokenMap();
        if (!isset(self::$tokenNamesTable[$characters]))
            return false;
        
        if (!isset(self::$tokenConstantLookupTable[$characters])) {
            $out = self::$tokenConstantLookupTable[$characters] = constant(__CLASS__ . '::' . self::$tokenNamesTable[$characters]);
            return true;
        }
        
        $out = self::$tokenConstantLookupTable[$characters];
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
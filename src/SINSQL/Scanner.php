<?php

namespace SINSQL;


class Scanner
{
    public function __construct()
    {
    }
    
    public function isWhitespace($characters)
    {
        return preg_match("/\s/i", $characters) > 0;
    }
    
    public function isNumber($characters)
    {
        return preg_match("/[0-9]+/i", $characters) > 0;
    }
    
    public function isCharacter($characters)
    {
        return preg_match("/[a-zA-Z!@#$%\^&*;]+/i", $characters) > 0;
    }
}
<?php

use SINSQL\Exceptions\FailedToParseException;
use SINSQL\Exceptions\IllegalCharacterException;
use SINSQL\Interfaces\IBuffer;
use SINSQL\Lexer;
use SINSQL\StringBuffer;
use SINSQL\Token;


class LexerTest extends PHPUnit_Framework_TestCase
{
    public function testStatementIsEmpty()
    {
        $statement = "";
        $scanner = new Lexer(new StringBuffer($statement));
        $actual = $scanner->getToken();
        $expected = Token::EOF;
        $this->assertEquals($expected, $actual);
    }
    
    public function testExceptionThrownOnParse()
    {
        $characters = "(:variable IS \"doomed\"^) AND (25 GREATER THAN OR IS :kool)";
        $scanner = new Lexer(new StringBuffer($characters));
        
        try{
            while ($scanner->getToken() != Token::EOF)
            {
        
            }
            $this->assertTrue(false, "An exception should be thrown.");
        } catch (IllegalCharacterException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testExceptionThrownOnUnmatchingQuotes()
    {
        $characters = "(:variable IS \"doomed\"\") AND (25 GREATER THAN OR IS :kool)";
        $scanner = new Lexer(new StringBuffer($characters));

        try {
            while ($scanner->getToken() != Token::EOF)
            {

            }
            $this->assertTrue(false, "An exception should be thrown.");
        } catch (FailedToParseException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testStringConsumesAllCharacters()
    {
        $characters = "\"0987654321`~abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()<>,./?:;'][{}\\|\"";
        $scanner = new Lexer(new StringBuffer($characters));
        while ($scanner->getToken() != Token::EOF)
        {
            
        }
        $this->assertTrue(true);
        
        $actual = $scanner->string();
        $expected = trim($characters, '"');
        $this->assertEquals($expected, $actual);
    }
    
    public function testTokensConsumed()
    {
        $characters = "(:variable IS \"doomed\") AND (25 GREATER THAN OR IS :kool) OR (:var IN (\"Awesome\", \"TEST\", \"Too soon\"))";
        $scanner = new Lexer(new StringBuffer($characters));
        $expected = 3;
        $scanner->getToken();
        $scanner->getToken();
        $scanner->getToken();
        $actual = $scanner->numOfTokensConsumed();
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testSkipNextTokens()
    {
        $characters = "(:variable IS \"doomed\") AND (25 GREATER THAN OR IS :kool) OR (:var IN (\"Awesome\", \"TEST\", \"Too soon\"))";
        $scanner = new Lexer(new StringBuffer($characters));
        $actual = $scanner->skipNextTokens(3);
        $expected = Token::TXT_SYMBOL;
        $this->assertEquals($expected, $actual);
    }
    
    public function testTokenizerWorks()
    {
        $characters = "(12 IS 12) AND (\"tree\" IS \"tree\")";
        $scanner = new Lexer(new StringBuffer($characters));
        
        while (($tok = $scanner->getToken()) != Token::EOF)
        {
            $holder = null;
            $tokenname = Token::stringify($tok);
            switch($tok)
            {
                case Token::TXT_STRING:
                    $holder = $scanner->string();
                    break;
                case Token::TXT_NUMBER:
                    $holder = $scanner->number();
                    break;
                case Token::TXT_SYMBOL:
                    $holder = $scanner->symbol();
                    break;
                default:
                    Token::parseToken($tok, $holder);
                    break;
            }
            
            echo $tokenname . " " . $holder;
            echo "\n";
        }
        
        $this->assertTrue(true);
    }
}

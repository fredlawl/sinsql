<?php

namespace SINSQLTest;

use SINSQL\Exceptions\FailedToParseException;
use SINSQL\Exceptions\IllegalCharacterException;
use SINSQL\Interfaces\IBuffer;
use SINSQL\Lexer;
use SINSQL\StringBuffer;
use SINSQL\Token;


class LexerTest extends \PHPUnit_Framework_TestCase
{
    public function testStatementIsEmpty()
    {
        $statement = "";
        $scanner = new Lexer(new StringBuffer($statement));
        $actual = $scanner->getToken();
        $expected = Token::TOK_EOF;
        $this->assertEquals($expected, $actual);
    }
    
    public function testExceptionThrownOnParse()
    {
        $characters = "(:variable IS \"doomed\"^) AND (25 GREATER THAN OR IS :kool)";
        $scanner = new Lexer(new StringBuffer($characters));
        
        try{
            while ($scanner->getToken() != Token::TOK_EOF)
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
            while ($scanner->getToken() != Token::TOK_EOF)
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
        while ($scanner->getToken() != Token::TOK_EOF)
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
        $actual = $scanner->skipNextTokens(4);
        $expected = Token::TOK_SYMBOL;
        $this->assertEquals($expected, $actual);
    }
    
    public function testTokenizerWorks()
    {
//        $characters = "(12 IS 12) AND (\"tree\" IS \"tree\")";
        $characters = "(12 IS 12) AND (12 IN [12, 1, 4, 5])";
        $scanner = new Lexer(new StringBuffer($characters));
        
        while (($tok = $scanner->getToken()) != Token::TOK_EOF)
        {
            $holder = null;
            $tokenname = Token::stringify($tok);
            switch($tok)
            {
                case Token::TOK_STRING:
                    $holder = $scanner->string();
                    break;
                case Token::TOK_NUMBER:
                    $holder = $scanner->number();
                    break;
                case Token::TOK_SYMBOL:
	                $holder = $scanner->symbol();
	                break;
                case Token::TOK_VARIABLE:
                    $holder = $scanner->variable();
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

<?php

use SINSQL\Exceptions\FailedToParseException;
use SINSQL\Exceptions\IllegalCharacterException;
use SINSQL\Interfaces\IBuffer;
use SINSQL\Lexer;
use SINSQL\StringBuffer;
use SINSQL\Token;


class LexerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Lexer
     */
    private $scanner;
    
    /**
     * @var IBuffer
     */
    private $buffer;
    
    private $sampleString;
    
    public function SetUp()
    {
        $this->sampleString = "(:variable IS \"doomed\") AND (25 GREATER THAN OR IS :kool) OR (:var IN (\"Awesome\", \"TEST\", \"Too soon\"))";
        $this->buffer = new StringBuffer($this->sampleString);
        $this->scanner = new Lexer($this->buffer);
    }
    
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
        $expected = 3;
        $this->scanner->getToken();
        $this->scanner->getToken();
        $this->scanner->getToken();
        $actual = $this->scanner->numOfTokensConsumed();
        
        $this->assertEquals($expected, $actual);
    }
    
    public function testSkipNextTokens()
    {
        $actual = $this->scanner->skipNextTokens(4);
        $expected = Token::TXT_SYMBOL;
        $this->assertEquals($expected, $actual);
    }
    
    public function testTokenizerWorks()
    {
        while (($tok = $this->scanner->getToken()) != Token::EOF)
        {
            $holder = null;
            $tokenname = Token::stringify($tok);
            switch($tok)
            {
                case Token::TXT_STRING:
                    $holder = $this->scanner->string();
                    break;
                case Token::TXT_NUMBER:
                    $holder = $this->scanner->number();
                    break;
                case Token::TXT_SYMBOL:
                    $holder = $this->scanner->symbol();
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

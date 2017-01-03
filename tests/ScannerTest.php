<?php

use SINSQL\Interfaces\IBuffer;
use SINSQL\Scanner;
use SINSQL\StringBuffer;
use SINSQL\Token;


class ScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Scanner
     */
    private $scanner;
    
    /**
     * @var IBuffer
     */
    private $buffer;
    
    private $sampleString;
    
    public function SetUp()
    {
        $this->sampleString = "(25 GREATER THAN OR IS 21) OR (1991 LESS THAN OR IS 1995)";
        $this->buffer = new StringBuffer($this->sampleString);
        $this->scanner = new Scanner($this->buffer);
    }
    
    public function testStringConsumesAllCharacters()
    {
        $characters = "\"0987654321`~abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()<>,./?:;'][{}\\|\"";
        $scanner = new Scanner(new StringBuffer($characters));
        while ($scanner->getToken() != Token::EOF)
        {
            
        }
        $this->assertTrue(true);
        
        $actual = $scanner->string();
        $expected = trim($characters, '"');
        $this->assertEquals($expected, $actual);
    }
    
    public function testFirstTokenIsLParan()
    {
        $actual = $this->scanner->getToken();
        $expected = Token::TXT_LEFTPARAN;
        $this->assertEquals($expected, $actual);
    }
    
    public function testNextTokenIsNumber()
    {
        $this->scanner->getToken();
        $actual = $this->scanner->getToken();
        $expected = Token::TXT_NUMBER;
        $this->assertEquals($expected, $actual);
        
        $expected = 25;
        $actual = $this->scanner->number();
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
        $actual = $this->scanner->skipNextTokens(19);
        $expected = Token::TXT_RIGHTPARAN;
        $this->assertEquals($expected, $actual);
    }
    
    public function testTokenizerWorks()
    {
        while ($this->scanner->getToken() != Token::EOF)
        {
            
        }
        
        $this->assertTrue(true);
    }
}

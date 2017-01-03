<?php

use SINSQL\Interfaces\IBuffer;
use SINSQL\TokenScanner;
use SINSQL\StringBuffer;
use SINSQL\Token;


class TokenScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var TokenScanner
     */
    private $scanner;
    
    /**
     * @var IBuffer
     */
    private $buffer;
    
    private $sampleString;
    
    public function SetUp()
    {
        $this->sampleString = "(:variable IS \"doomed\") AND (25 GREATER THAN OR IS 21) OR (:var IN (\"Awesome\", \"TEST\", \"Too soon\"))";
        $this->buffer = new StringBuffer($this->sampleString);
        $this->scanner = new TokenScanner($this->buffer);
    }
    
    public function testStringConsumesAllCharacters()
    {
        $characters = "\"0987654321`~abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()<>,./?:;'][{}\\|\"";
        $scanner = new TokenScanner(new StringBuffer($characters));
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
        $actual = $this->scanner->skipNextTokens(8);
        $expected = Token::TXT_CHARACTER;
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

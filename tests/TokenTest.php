<?php

use \SINSQL\Token;

class TokenTest extends PHPUnit_Framework_TestCase
{
    public function testParseTokenReturnsPrettyToken()
    {
        $actual = null;
        $returnResult = Token::parseToken(Token::TOK_COLON, $actual);
        $expected = ":";
        
        $this->assertTrue($returnResult);
        $this->assertEquals($expected, $actual);
    }
    
    public function testGetTokenReturnsToken()
    {
        $actual = null;
        $returnResult = Token::getToken(")", $actual);
        $expected = Token::TOK_RIGHTPAREN;
    
        $this->assertTrue($returnResult);
        $this->assertEquals($expected, $actual);
    }
    
    public function testParseTokenDoesNotReturnPrettyToken()
    {
        $actual = null;
        $returnResult = Token::parseToken(200000, $actual);
    
        $this->assertFalse($returnResult);
        $this->assertTrue(is_null($actual));
    }
    
    public function testGetTokenDoesNotReturnToken()
    {
        $actual = null;
        $returnResult = Token::getToken(';lkj;lkj;asdf', $actual);
        
        $this->assertFalse($returnResult);
        $this->assertTrue(is_null($actual));
    }
}

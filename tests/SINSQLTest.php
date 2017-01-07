<?php

use SINSQL\SINSQL;

class SINSQLTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SINSQL
     */
    private $parser;
    
    public function SetUp()
    {
        $this->parser = new SINSQL();
    }
    
    public function testEmptyString()
    {
        $this->assertFalse($this->parser->parse(""));
    }
    
    public function testAlotOfWhitespace()
    {
        try {
            $this->parser->parse("\n     \t      \n\t");
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testSingleNumber()
    {
        try {
            $this->parser->parse("12");
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testSingleSymbol()
    {
        try {
            $this->parser->parse("asdljkflaksdjfasdf");
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testSingleString()
    {
        try {
            $this->parser->parse("\"test\"");
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testRunawayString()
    {
        try {
            $this->parser->parse("\"test");
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testInvalidToken()
    {
        try {
            $this->parser->parse("($\"test\" IS \"test\")");
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testNumberIs()
    {
        $this->assertTrue($this->parser->parse("14 IS 14"));
        $this->assertFalse($this->parser->parse("14 IS 15"));
    }
    
    public function testNumberIsNot()
    {
        $this->assertFalse($this->parser->parse("14 IS NOT 14"));
        $this->assertTrue($this->parser->parse("14 IS NOT 15"));
    }
    
    public function testNumberGreaterThan()
    {
        $this->assertFalse($this->parser->parse("14 GREATER THAN 14"));
        $this->assertFalse($this->parser->parse("14 GREATER THAN 15"));
        $this->assertTrue($this->parser->parse("15 GREATER THAN 14"));
    }
    
    public function testNumberGreaterThanOrIs()
    {
        $this->assertTrue($this->parser->parse("14 GREATER THAN OR IS 14"));
        $this->assertFalse($this->parser->parse("14 GREATER THAN OR IS 15"));
        $this->assertTrue($this->parser->parse("15 GREATER THAN OR IS 14"));
    }
    
    public function testNumberLessThan()
    {
        $this->assertFalse($this->parser->parse("14 LESS THAN 14"));
        $this->assertTrue($this->parser->parse("14 LESS THAN 15"));
        $this->assertFalse($this->parser->parse("15 LESS THAN 14"));
    }
    
    public function testNumberLessThanOrIs()
    {
        $this->assertTrue($this->parser->parse("14 LESS THAN OR IS 14"));
        $this->assertTrue($this->parser->parse("14 LESS THAN OR IS 15"));
        $this->assertFalse($this->parser->parse("15 LESS THAN OR IS 14"));
    }
    
    public function testNumberAnd()
    {
        $this->assertTrue($this->parser->parse("14 AND 14"));
        $this->assertTrue($this->parser->parse("14 AND 15"));
    }
    
    public function testNumberOr()
    {
        $this->assertTrue($this->parser->parse("14 OR 14"));
        $this->assertTrue($this->parser->parse("14 OR 15"));
    }
    
    public function testNumberIn()
    {
        $this->assertTrue($this->parser->parse("14 IN [10, 12, 13, 14]"));
        $this->assertFalse($this->parser->parse("14 IN [1, 2, 3, 4]"));
    }
    
    public function testNumberNotIn()
    {
        $this->assertFalse($this->parser->parse("14 NOT IN [10, 12, 13, 14]"));
        $this->assertTrue($this->parser->parse("14 NOT IN [1, 2, 3, 4]"));
    }
    
    public function testStringCaseInsensitiveIs()
    {
        $this->assertTrue($this->parser->parse("\"test\" IS \"test\""));
        $this->assertTrue($this->parser->parse("\"test\" IS \"TEST\""));
    }
    
}

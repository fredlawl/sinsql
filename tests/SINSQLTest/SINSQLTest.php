<?php

namespace SINSQLTest;

use SINSQL\Interfaces\IVariableMapper;
use SINSQL\SINSQL;

class SINSQLTest extends \PHPUnit_Framework_TestCase
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
    
    public function testIs()
    {
        $this->assertTrue($this->parser->parse("14 IS 14"));
        $this->assertFalse($this->parser->parse("14 IS 15"));
        
        $this->assertTrue($this->parser->parse("\"king\" IS \"king\""));
        $this->assertFalse($this->parser->parse("\"king\" IS \"kong\""));
    }
    
    public function testIsNot()
    {
        $this->assertFalse($this->parser->parse("14 IS NOT 14"));
        $this->assertTrue($this->parser->parse("14 IS NOT 15"));
    
        $this->assertFalse($this->parser->parse("\"king\" IS NOT \"king\""));
        $this->assertTrue($this->parser->parse("\"king\" IS NOT \"kong\""));
    }
    
    public function testGreaterThan()
    {
        $this->assertFalse($this->parser->parse("14 GREATER THAN 14"));
        $this->assertFalse($this->parser->parse("14 GREATER THAN 15"));
        $this->assertTrue($this->parser->parse("15 GREATER THAN 14"));
    
        $this->assertFalse($this->parser->parse("\"king\" GREATER THAN \"kong\""));
        $this->assertTrue($this->parser->parse("\"king\" GREATER THAN \"apples\""));
        $this->assertFalse($this->parser->parse("\"king\" GREATER THAN \"king\""));
    }
    
    public function testGreaterThanOrIs()
    {
        $this->assertTrue($this->parser->parse("14 GREATER THAN OR IS 14"));
        $this->assertFalse($this->parser->parse("14 GREATER THAN OR IS 15"));
        $this->assertTrue($this->parser->parse("15 GREATER THAN OR IS 14"));
    
        $this->assertFalse($this->parser->parse("\"king\" GREATER THAN OR IS \"kong\""));
        $this->assertTrue($this->parser->parse("\"king\" GREATER THAN OR IS \"apples\""));
        $this->assertTrue($this->parser->parse("\"king\" GREATER THAN OR IS \"king\""));
    }
    
    public function testLessThan()
    {
        $this->assertFalse($this->parser->parse("14 LESS THAN 14"));
        $this->assertTrue($this->parser->parse("14 LESS THAN 15"));
        $this->assertFalse($this->parser->parse("15 LESS THAN 14"));
    
        $this->assertTrue($this->parser->parse("\"king\" LESS THAN \"kong\""));
        $this->assertFalse($this->parser->parse("\"king\" LESS THAN \"apples\""));
        $this->assertFalse($this->parser->parse("\"king\" LESS THAN \"king\""));
    }
    
    public function testLessThanOrIs()
    {
        $this->assertTrue($this->parser->parse("14 LESS THAN OR IS 14"));
        $this->assertTrue($this->parser->parse("14 LESS THAN OR IS 15"));
        $this->assertFalse($this->parser->parse("15 LESS THAN OR IS 14"));
    
        $this->assertTrue($this->parser->parse("\"king\" LESS THAN OR IS \"kong\""));
        $this->assertFalse($this->parser->parse("\"king\" LESS THAN OR IS \"apples\""));
        $this->assertTrue($this->parser->parse("\"king\" LESS THAN OR IS \"king\""));
    }
    
    public function testAnd()
    {
        $this->assertTrue($this->parser->parse("14 AND 14"));
        $this->assertTrue($this->parser->parse("14 AND 15"));
    
        $this->assertTrue($this->parser->parse("\"king\" AND \"king\""));
        $this->assertTrue($this->parser->parse("\"king\" AND \"kong\""));
    }
    
    public function testOr()
    {
        $this->assertTrue($this->parser->parse("14 OR 14"));
        $this->assertTrue($this->parser->parse("14 OR 15"));
    
        $this->assertTrue($this->parser->parse("\"king\" OR \"king\""));
        $this->assertTrue($this->parser->parse("\"king\" OR \"kong\""));
    }
    
    public function testIn()
    {
        $this->assertTrue($this->parser->parse("14 IN [10, 12, 13, 14]"));
        $this->assertFalse($this->parser->parse("14 IN [1, 2, 3, 4]"));
    
        $this->assertTrue($this->parser->parse("\"king\" IN [\"apples\", \"kong\", \"king\"]"));
        $this->assertFalse($this->parser->parse("\"Cardinals\" IN [\"apples\", \"kong\", \"king\"]"));
    }
    
    public function testNotIn()
    {
        $this->assertFalse($this->parser->parse("14 NOT IN [10, 12, 13, 14]"));
        $this->assertTrue($this->parser->parse("14 NOT IN [1, 2, 3, 4]"));
        
        $this->assertFalse($this->parser->parse("\"king\" NOT IN [\"apples\", \"kong\", \"king\"]"));
        $this->assertTrue($this->parser->parse("\"Cardinals\" NOT IN [\"apples\", \"kong\", \"king\"]"));
    }
    
    public function testStringCaseInsensitiveIs()
    {
        $this->assertTrue($this->parser->parse("\"test\" IS \"test\""));
        $this->assertTrue($this->parser->parse("\"test\" IS \"TEST\""));
    }
    
    public function testVariableReplaces()
    {
        $mapper = \Mockery::mock(IVariableMapper::class)
            ->shouldReceive('map')
            ->andReturn(14)
            ->mock();
    
        $parser = new SINSQL($mapper);
        $this->assertTrue($parser->parse(":myvariable IS 14"));
        
        
        $mapper = \Mockery::mock(IVariableMapper::class)
            ->shouldReceive('map')
            ->andReturn("king")
            ->mock();
        
        $parser = new SINSQL($mapper);
        $this->assertTrue($parser->parse(":myvariable IS \"king\""));
    
        $parser = new SINSQL($mapper);
        $this->assertTrue($parser->parse(":myvariable IS \"KING\""));
    }
    
    
    public function testVariableHasNumbersCharactersOrUnderscores()
    {
	    $mapper = \Mockery::mock(IVariableMapper::class)
	                      ->shouldReceive('map')
	                      ->andReturn(14)
	                      ->mock();
	
	    $parser = new SINSQL($mapper);
	    $this->assertTrue($parser->parse(":__12_Variable12 IS 14"));
    }
    
    
    // Since all the basecases have been accounted for, lets have some recursive fun
    public function testEverything()
    {
        $query = "((25 GREATER THAN OR IS 21) OR (1991 LESS THAN OR IS 1995)) AND (\"king\" NOT IN [\"apples\", \"kong\", \"king\"])";
        $this->assertFalse($this->parser->parse($query));
    }
    
}

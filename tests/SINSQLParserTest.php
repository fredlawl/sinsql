<?php

use SINSQL\Exceptions\SINQLException;
use SINSQL\Exceptions\TokenMismatchException;
use SINSQL\Interfaces\IVariableMapper;
use SINSQL\SINSQLParser;
use SINSQL\StringBuffer;


class SINSQLParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var IVariableMapper
     */
    private $variableMapper;
    
    private $mockVariable;
    
    public function SetUp()
    {
        $this->mockVariable = 'mockedVariable';
        $this->variableMapper = \Mockery::mock(IVariableMapper::class)
            ->shouldReceive('map')
            ->andReturn($this->mockVariable)
            ->mock();
    }
    
    public function testParseReturnsFalseOnEmptyInput()
    {
        $parser = new SINSQLParser(new StringBuffer(""));
        $this->assertFalse($parser->parse());
    }
    
    public function testTokenMismatch()
    {
        try {
            $parser = new SINSQLParser(new StringBuffer("::somevar"));
            $parser->generateParseTree()->evaluate();
            $this->assertTrue(false, "This test should've failed");
        } catch (TokenMismatchException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testGarbage()
    {
        $parser = new SINSQLParser(new StringBuffer("garbage"));
//        try {
        $parser->generateParseTree();
//        } catch (SINQLException $e) {
//            $this->expectException("Unable to construct parse tree.");
//        }
    }
    
    public function testParse()
    {
        $expected = "\"Somestring\"";
        $parser = new SINSQLParser(new StringBuffer($expected));
        $this->assertEquals(trim($expected, '"'), $parser->generateParseTree()->evaluate());
    
        $expected = 123456;
        $parser = new SINSQLParser(new StringBuffer($expected));
        $this->assertEquals($expected, $parser->generateParseTree()->evaluate());
        
        $expected = $this->mockVariable;
        $parser = new SINSQLParser(new StringBuffer(":" . $expected), $this->variableMapper);
        $this->assertEquals($expected, $parser->generateParseTree()->evaluate());
    }
    
}

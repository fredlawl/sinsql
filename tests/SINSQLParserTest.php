<?php

use SINSQL\Exceptions\FailedToParseException;
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
    
    public function testInvalidExpressionThrowsError()
    {
        $expected = "(12 GOOP 12)";
        $parser = new SINSQLParser(new StringBuffer($expected));
        
        try {
            $parser->generateParseTree();
            $this->assertTrue(false, "This should not be reached.");
        } catch (FailedToParseException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testGarbage()
    {
        $parser = new SINSQLParser(new StringBuffer("garbage"));
        try {
            $parser->generateParseTree();
            $this->assertTrue(false, "This should not be reached.");
        } catch (SINQLException $e) {
            $this->assertTrue(true);
        }
    
        $parser = new SINSQLParser(new StringBuffer("1 AND"));
        try {
            $parser->generateParseTree();
            $this->assertTrue(false, "This should not be reached.");
        } catch (SINQLException $e) {
            $this->assertTrue(true);
        }
    
        $parser = new SINSQLParser(new StringBuffer("1 AND yuck"));
        try {
            $parser->generateParseTree();
            $this->assertTrue(false, "This should not be reached.");
        } catch (SINQLException $e) {
            $this->assertTrue(true);
        }
        
        $parser = new SINSQLParser(new StringBuffer("1234"));
        try {
            $parser->generateParseTree();
            $this->assertTrue(false, "This should not be reached.");
        } catch (SINQLException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testNumberEquality()
    {
        $expected = "12 IS 12";
        $parser = new SINSQLParser(new StringBuffer($expected));
        $this->assertTrue($parser->generateParseTree()->evaluate());
    }
    
    
    /**
     * In order for this test to work, there needs to be a
     * string comparer injected into the parser.
     */
//    public function testStringEquality()
//    {
//        $expected = "\"test\" IS \"TEST\"";
//        $parser = new SINSQLParser(new StringBuffer($expected));
//        $this->assertTrue($parser->generateParseTree()->evaluate());
//    }
    

    public function testMultipleExpressions()
    {
//        $expected = "(12 IS 12) AND ((\"tree\" IS \"TrEe\") AND (13 IS 13))";
        $expected = "(12 IS 12) AND (\"tree\" IS \"tree\")";
        $parser = new SINSQLParser(new StringBuffer($expected));
        $tree = $parser->generateParseTree();
        $this->assertTrue($tree->evaluate());
    }
    
    public function testNestedExpressions()
    {
        $expected = "(\"Bob\" IS \"bob\") OR ((12 IS 12) AND (\"tree\" IS \"tree\"))";
        $parser = new SINSQLParser(new StringBuffer($expected));
        $tree = $parser->generateParseTree();
        var_dump($tree);
        $this->assertTrue($tree->evaluate());
    }
    
}

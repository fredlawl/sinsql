<?php

use SINSQL\Exceptions\SINQLException;
use SINSQL\SINSQLParser;
use SINSQL\StringBuffer;


class SINSQLParserTest extends PHPUnit_Framework_TestCase
{
    public function testParseReturnsFalseOnEmptyInput()
    {
        $parser = new SINSQLParser(new StringBuffer(""));
        $this->assertFalse($parser->parse());
    }
    
    public function testParse()
    {
        $expected = "\"Somestring\"";
        $parser = new SINSQLParser(new StringBuffer($expected));
        $this->assertEquals(trim($expected, '"'), $parser->generateParseTree()->evaluate());
    
        $expected = 123456;
        $parser = new SINSQLParser(new StringBuffer($expected));
        $this->assertEquals($expected, $parser->generateParseTree()->evaluate());
        
        $expected = "somevar";
        $parser = new SINSQLParser(new StringBuffer(":" . $expected));
        $this->assertEquals($expected, $parser->generateParseTree()->evaluate());
    
        $expected = "somevar";
        $parser = new SINSQLParser(new StringBuffer("::" . $expected));
        $this->assertEquals($expected, $parser->generateParseTree()->evaluate());
    
        $parser = new SINSQLParser(new StringBuffer("garbage"));
//        try {
            $parser->generateParseTree();
//        } catch (SINQLException $e) {
//            $this->expectException("Unable to construct parse tree.");
//        }
        
    }
    
}

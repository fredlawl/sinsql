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
    
}

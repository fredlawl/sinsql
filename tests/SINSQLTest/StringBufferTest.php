<?php

namespace SINSQLTest;

use SINSQL\Interfaces\IBuffer;
use SINSQL\StringBuffer;

class StringBufferTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IBuffer
     */
    private $buffer;
    private $sampleString;
    
    public function SetUp()
    {
        $this->sampleString = "this is a sample string\na new line\n\n\n";
        $this->buffer = new StringBuffer($this->sampleString);
    }
    
    public function testBufferGetsCharacter()
    {
        $expected = 't';
        $actual = $this->buffer->get();
        $this->assertEquals($expected, $actual);
    }
    
    public function testBufferGetsNewLine()
    {
        for ($i = 0; $i < 23; $i++) {
            $this->buffer->get();
        }
        
        $expected = "a";
        $actual = $this->buffer->get();
        $this->assertEquals($expected, $actual);
    }
    
    public function testBufferReachesEOF()
    {
        $expected = "this is a sample stringa new line";
        $actual = "";
        while (($char = $this->buffer->get()) != null)
        {
            $actual .= $char;
        }
        
        $this->assertEquals($expected, $actual);
    }
}

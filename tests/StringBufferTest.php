<?php

use SINSQL\Interfaces\IBuffer;
use SINSQL\StringBuffer;

class StringBufferTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var IBuffer
     */
    private $buffer;
    
    public function SetUp()
    {
        $sampleString = "this is a sample string\na new line\n\n\n";
        $this->buffer = new StringBuffer($sampleString);
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
        while ($this->buffer->get() != null)
        {
            
        }
        
        $this->assertTrue(true);
    }
}

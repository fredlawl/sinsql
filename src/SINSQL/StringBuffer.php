<?php

namespace SINSQL;


use SINSQL\Interfaces\IBuffer;

class StringBuffer implements IBuffer
{
    private $lineNumber = 0;
    private $columnNumber = 0;
    private $line = "";
    private $input;
    private $eof;
    
    public function __construct($input)
    {
        $this->input = explode("\n", $input);
        $this->eof = count($this->input);
    }
    
    public function get()
    {
        $this->columnNumber++;
        $lineLength = strlen($this->line);
        if ($this->columnNumber >= $lineLength) {
            
            if ($this->lineNumber == $this->eof)
                return null;
            
            $line = $this->input[$this->lineNumber];
            $this->columnNumber = 0;
            $this->lineNumber++;
            $this->line = $line;
            $lineLength = strlen($line);
        }
        
        if ($lineLength == 0)
            return $this->get();
        
        return $this->line[$this->columnNumber];
    }
    
    public function reset()
    {
        $this->line = "";
        $this->lineNumber = 0;
        $this->columnNumber = 0;
    }
    
    public function currentLine()
    {
        return $this->lineNumber - 1;
    }
    
    public function currentColumn()
    {
        return $this->columnNumber - 1;
    }
}
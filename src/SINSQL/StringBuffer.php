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
    private $isEOF;
    
    public function __construct($input)
    {
        $this->isEOF = empty($input);
        $this->input = explode("\n", $input);
        $this->eof = count($this->input);
    }
    
    public function get()
    {
        $this->columnNumber++;
        $lineLength = strlen($this->line);
        
        if ($this->columnNumber >= $lineLength) {
            
            if ($this->lineNumber == $this->eof) {
                $this->isEOF = true;
                return null;
            }
            
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
        return $this->lineNumber;
    }
    
    public function currentColumn()
    {
        return $this->columnNumber - 1;
    }
    
    public function isEOF()
    {
        return $this->isEOF;
    }
    
    public function displayLineColumn()
    {
        return $this->currentLine() . ":" . $this->currentColumn();
    }
}
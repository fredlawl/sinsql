<?php

namespace SINSQL;


use SINSQL\Interfaces\IBuffer;

class StringBuffer implements IBuffer
{
    private $currentLineIndex = 0;
    private $currentColumnIndex = 0;
    private $currentLine = "";
    private $input;
    private $numberOfLines;
    private $eof;
    
    public function __construct($input)
    {
        $this->eof = empty($input);
        $this->input = explode("\n", $input);
        $this->numberOfLines = count($this->input);
    }
    
    public function get()
    {
        ++$this->currentColumnIndex;
        $lineLength = strlen($this->currentLine);
        
        if ($this->currentColumnIndex >= $lineLength) {
            
            if ($this->currentLineIndex == $this->numberOfLines) {
                $this->eof = true;
                return null;
            }
            
            $line = $this->input[$this->currentLineIndex];
            $this->currentColumnIndex = 0;
            ++$this->currentLineIndex;
            $this->currentLine = $line;
            
            $lineLength = strlen($line);
        }
        
        if ($lineLength == 0)
            return $this->get();
        
        return $this->currentLine[$this->currentColumnIndex];
    }
    
    public function reset()
    {
        $this->currentLine = "";
        $this->currentLineIndex = 0;
        $this->currentColumnIndex = 0;
    }
    
    public function currentLine()
    {
        return $this->currentLineIndex;
    }
    
    public function currentColumn()
    {
        return $this->currentColumnIndex - 1;
    }
    
    public function isEOF()
    {
        return $this->eof;
    }
    
    public function displayLineColumn()
    {
        return $this->currentLine() . ":" . $this->currentColumn();
    }
}
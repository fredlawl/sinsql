<?php

namespace SINSQL;


use SINSQL\Exceptions\IllegalCharacterException;
use SINSQL\Interfaces\IBuffer;

class Scanner
{
    /**
     * @var IBuffer
     */
    private $buffer;
    
    private $nextCharacter = " ";
    private $currentCharacter = " ";
    
    private $number = 0;
    private $string = "";
    
    private $token;
    private $numberOfTokensConsumed = 0;
    
    public function __construct(IBuffer $buffer)
    {
        $this->buffer = $buffer;
    }
    
    public function getToken()
    {
        // Immediate close if done scanning
        if (is_null($this->nextCharacter))
            return Token::EOF;
        
        // Ignore whitespace
        while ($this->isWhitespace())
            $this->nextCharacter();
        
        // parse a number
        if ($this->isDigit()) {
            $this->number = $this->parseNumber();
            $this->token = Token::TXT_NUMBER;
            $this->numberOfTokensConsumed++;
            return $this->token;
        }
        
        // parse a string
        if ($this->isQuote()) {
            $this->string = $this->parseString();
            $this->token = Token::TXT_STRING;
            $this->numberOfTokensConsumed++;
            return $this->token;
        }
        
        // parse a character
        if ($this->isAllowableCharacter()) {
            $this->nextCharacter();
            $this->token = Token::TXT_CHARACTER;
        } else {
            $char = $this->currentCharacter;
            $this->nextCharacter();
            
            if (!Token::getToken($char, $this->token)) {
                throw new IllegalCharacterException(
                    $char,
                    $this->buffer->currentLine(),
                    $this->buffer->currentColumn());
            }
        }
        
        $this->numberOfTokensConsumed++;
        return $this->token;
    }
    
    public function skipNextTokens($number)
    {
        $token = null;
        for ($i = 0; $i < $number; $i++) {
            $token = $this->getToken();
        }
        
        return $token;
    }
    
    public function numOfTokensConsumed()
    {
        return $this->numberOfTokensConsumed;
    }
    
    public function character()
    {
        return $this->currentCharacter;
    }
    
    public function string()
    {
        return $this->string;
    }
    
    public function number()
    {
        return $this->number;
    }
    
    public function parseNumber()
    {
        $result = 0;
        do {
            $result = $result * 10 + intval($this->currentCharacter);
            $this->nextCharacter();
        } while ($this->isDigit());
        
        return $result;
    }
    
    public function parseString()
    {
        // To ignore that first quote
        $this->nextCharacter();
        
        $result = "";
        
        do {
            $result = $result . $this->currentCharacter;
            $this->nextCharacter();
        } while (!$this->isQuote());
        
        return $result;
    }
    
    public function nextCharacter()
    {
        $this->currentCharacter = $this->nextCharacter;
        $this->nextCharacter = $this->buffer->get();
    }
    
    public function isWhitespace()
    {
        return preg_match("/\s/i", $this->currentCharacter) > 0;
    }
    
    public function isDigit()
    {
        return preg_match("/[0-9]+/i", $this->currentCharacter) > 0;
    }
    
    public function isAllowableCharacter()
    {
        return preg_match("/[a-zA-Z]+/i", $this->currentCharacter) > 0;
    }
    
    public function isQuote()
    {
        return $this->currentCharacter == '"';
    }
}
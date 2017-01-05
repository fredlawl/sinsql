<?php

namespace SINSQL;


use SINSQL\Exceptions\FailedToParseException;
use SINSQL\Exceptions\IllegalCharacterException;
use SINSQL\Interfaces\IBuffer;

class Lexer
{
    /**
     * @var IBuffer
     */
    private $buffer;
    
    private $nextCharacter = "";
    private $currentCharacter = "";
    
    private $number = 0;
    private $string = "";
    private $identifier = '';
    private $symbol = '';
    
    private $token;
    private $numberOfTokensConsumed = 0;
    
    public function __construct(IBuffer $buffer)
    {
        $this->buffer = $buffer;
    }
    
    public function getToken()
    {
        // Immediate close if done scanning
        if ($this->buffer->isEOF())
            return Token::EOF;
        
        // Advance the buffer to negate the start.
        if (empty($this->currentCharacter)) {
            $this->nextCharacter();
            $this->nextCharacter();
        }
        
        // parse whitespace
        if ($this->isWhitespace()) {
            $this->consumeWhitespace();
            $this->token = Token::TXT_SPACE;
            $this->numberOfTokensConsumed++;
            return $this->token;
        }
        
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
            $this->symbol = $this->parseSymbol();
            $this->token = Token::TXT_SYMBOL;
        } else {
            $char = $this->currentCharacter;
            $this->nextCharacter();
            
            if (!Token::getToken($char, $this->token)) {
                throw new IllegalCharacterException(
                    $char,
                    $this->lineColumn()
                );
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
    
    public function string()
    {
        return $this->string;
    }
    
    public function number()
    {
        return $this->number;
    }
    
    public function symbol()
    {
        return $this->symbol;
    }
    
    private function parseNumber()
    {
        $result = 0;
        do {
            $result = $result * 10 + intval($this->currentCharacter);
            $this->nextCharacter();
        } while ($this->isDigit());
        
        return $result;
    }
    
    private function parseString()
    {
        // To ignore that first quote
        $this->nextCharacter();
    
        $hasClosingQuote = false;
        $lineColumn = $this->lineColumn();
        
        $result = "";
        
        do {
            $result = $result . $this->currentCharacter;
            $this->nextCharacter();
        } while (!$this->isQuote() && !$this->buffer->isEOF());
    
        if ($this->isQuote())
            $hasClosingQuote = true;
        
        if ($this->buffer->isEOF() && !$hasClosingQuote)
            throw new FailedToParseException("Expected closing quotation mark", $lineColumn);
        
        // Proceed to next token to not do infinite loops :p
        $this->nextCharacter();
        
        return $result;
    }
    
    private function parseSymbol()
    {
        $this->nextCharacter();
        
        $result = $this->identifier;
        do {
            $result = $result . $this->currentCharacter;
            $this->nextCharacter();
        } while ($this->isAllowableCharacter());
        
        return $result;
    }
    
    private function consumeWhitespace()
    {
        do {
            $this->nextCharacter();
        } while ($this->isWhitespace());
    }
    
    private function nextCharacter()
    {
        $this->identifier = $this->currentCharacter;
        $this->currentCharacter = $this->nextCharacter;
        $this->nextCharacter = $this->buffer->get();
    }
    
    private function isWhitespace()
    {
        return preg_match("/\s/", $this->currentCharacter) > 0;
    }
    
    private function isDigit()
    {
        return preg_match("/[0-9]/", $this->currentCharacter) > 0;
    }
    
    private function isAllowableCharacter()
    {
        return preg_match("/[a-zA-Z]/", $this->currentCharacter) > 0;
    }
    
    private function isQuote()
    {
        return $this->currentCharacter == '"';
    }
    
    public function lineColumn()
    {
        return $this->buffer->displayLineColumn();
    }
}
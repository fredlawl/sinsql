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
    private $character = "";
    
    private $number = 0;
    private $string = "";
    private $identifier = '';
    private $symbol = '';
    
    private $numberOfTokensConsumed = 0;
    
    public function __construct(IBuffer& $buffer)
    {
        $this->buffer = $buffer;
        $this->character = $this->buffer->get();
        $this->identifier = $this->character;
        $this->nextCharacter = $this->buffer->get();
    }
    
    public function getToken()
    {
        $tmpToken = Token::EOF;
        
        // Immediate close if done scanning
        if ($this->character == null)
            return Token::EOF;
        
        // parse whitespace
        if ($this->isWhitespace()) {
            $this->consumeWhitespace();
            ++$this->numberOfTokensConsumed;
            return Token::TXT_SPACE;
        }
        
        // parse a number
        if ($this->isDigit()) {
            $this->number = $this->parseNumber();
            ++$this->numberOfTokensConsumed;
            return Token::TXT_NUMBER;
        }
        
        // parse a string
        if ($this->isQuote()) {
            $this->string = $this->parseString();
            ++$this->numberOfTokensConsumed;
            return Token::TXT_STRING;
        }
        
        // parse a character
        if ($this->isAllowableCharacter()) {
            $this->symbol = $this->parseSymbol();
            $tmpToken = Token::TXT_SYMBOL;
        } else {
            $char = $this->character;
            $this->advanceBuffer();
            
            if (!Token::getToken($char, $tmpToken)) {
                throw new IllegalCharacterException(
                    $char,
                    $this->lineColumn()
                );
            }
        }
        
        ++$this->numberOfTokensConsumed;
        return $tmpToken;
    }
    
    public function skipNextTokens($number)
    {
        $token = null;
        for ($i = 0; $i < $number; ++$i) {
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
            $result = $result * 10 + intval($this->character);
            $this->advanceBuffer();
        } while ($this->isDigit());
        
        return $result;
    }
    
    private function parseString()
    {
        $lineColumn = $this->lineColumn();
        
        $result = "";
    
        do {
            $this->advanceBuffer();
            $result .= $this->character;
        } while ($this->nextCharacter != '"' && $this->nextCharacter != null);
    
        $this->advanceBuffer();
        if ($this->character != '"') {
            throw new FailedToParseException("Expected closing quotation mark", $lineColumn);
        }
        
        $this->advanceBuffer();
        return $result;
    }
    
    private function parseSymbol()
    {
        $this->advanceBuffer();
        
        $result = $this->identifier;
        do {
            $result = $result . $this->character;
            $this->advanceBuffer();
        } while ($this->isAllowableCharacter());
        
        return $result;
    }
    
    private function consumeWhitespace()
    {
        do {
            $this->advanceBuffer();
        } while ($this->isWhitespace());
    }
    
    private function advanceBuffer()
    {
        $this->identifier = $this->character;
        $this->character = $this->nextCharacter;
        $this->nextCharacter = $this->buffer->get();
    }
    
    private function isWhitespace()
    {
        return ctype_space($this->character);
    }
    
    private function isDigit()
    {
        return ctype_digit($this->character);
    }
    
    private function isAllowableCharacter()
    {
        return ctype_alpha($this->character);
    }
    
    private function isQuote()
    {
        return $this->character == '"';
    }
    
    public function lineColumn()
    {
        return $this->buffer->displayLineColumn();
    }
}
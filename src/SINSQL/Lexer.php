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
    
    private $numberOfTokensConsumed = 0;
    
    public function __construct(IBuffer& $buffer)
    {
        $this->buffer = $buffer;
        $this->currentCharacter = $this->buffer->get();
        $this->identifier = $this->currentCharacter;
        $this->nextCharacter = $this->buffer->get();
    }
    
    public function getToken()
    {
        $tmpToken = Token::EOF;
        
        // Immediate close if done scanning
        if ($this->currentCharacter == null)
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
            $char = $this->currentCharacter;
            $this->nextCharacter();
            
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
            $result = $result * 10 + intval($this->currentCharacter);
            $this->nextCharacter();
        } while ($this->isDigit());
        
        return $result;
    }
    
    private function parseString()
    {
        $lineColumn = $this->lineColumn();
        
        $result = "";
    
        do {
            $this->nextCharacter();
            $result .= $this->currentCharacter;
        } while ($this->nextCharacter != '"' && $this->nextCharacter != null);
    
        $this->nextCharacter();
        if ($this->currentCharacter != '"') {
            throw new FailedToParseException("Expected closing quotation mark", $lineColumn);
        }
        
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
        return ctype_space($this->currentCharacter);
    }
    
    private function isDigit()
    {
        return ctype_digit($this->currentCharacter);
    }
    
    private function isAllowableCharacter()
    {
        return ctype_alpha($this->currentCharacter);
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
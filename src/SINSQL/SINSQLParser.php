<?php

namespace SINSQL;

use SINSQL\Exceptions\FailedToParseException;
use SINSQL\Exceptions\SINQLException;
use SINSQL\Exceptions\TokenMismatchException;
use SINSQL\Interfaces\IBuffer;
use SINSQL\Interfaces\ITerm;
use SINSQL\Interfaces\IVariableMapper;
use SINSQL\Operands\Variable;

class SINSQLParser
{
    /**
     * @var Lexer
     */
    private $lexer;
    
    /**
     * @var IVariableMapper
     */
    private $variableMapper;
    
    private $nextToken = Token::EOF;
    private $currentToken = null;
    
    
    public function __construct(IBuffer $buffer, IVariableMapper $variableMapper = null)
    {
        $this->lexer = new Lexer($buffer);
        $this->variableMapper = $variableMapper;
    }
    
    
    /**
     * @return bool
     * @throws SINQLException
     */
    public function parse()
    {
        $tree = $this->generateParseTree();
        
        if (is_null($tree))
            return false;
        
        return boolval($tree->evaluate());
    }
    
    
    /**
     * @return ITerm
     */
    public function generateParseTree()
    {
        $this->advanceToken();
        if ($this->matches(Token::EOF))
            return null;
        
        return $this->expression();
    }
    
    
    /**
     * @return ITerm
     */
    private function expression()
    {
        $this->advanceToken();
        return $this->left();
    }
    
    
    /**
     * @return ITerm
     */
    private function left()
    {
        $left = null;
        if (
            $this->currentTokenMatches(Token::TXT_COLON) ||
            $this->currentTokenMatches(Token::TXT_NUMBER) ||
            $this->currentTokenMatches(Token::TXT_STRING)
        ) {
            $left = $this->term();
        } else {
            if (!$this->isEOF())
                $left = $this->expression();
        }
        
        $this->advanceToken();
        return $left;
    }
    
    /**
     * @return ITerm
     * @throws SINQLException
     */
    private function term()
    {
        $return = null;
        if ($this->currentTokenMatches(Token::TXT_COLON)) {
            $return = $this->variable();
        }
        
        if ($this->currentTokenMatches(Token::TXT_NUMBER)) {
            // TODO: Change to a different type.
            $return = new Variable($this->lexer->number());
        }
    
        if ($this->currentTokenMatches(Token::TXT_STRING)) {
            $return = new Variable($this->lexer->string());
        }
        
        if (is_null($return)) {
            throw new SINQLException("Invalid call to function " . __CLASS__ . "::term().");
        }
        
        $this->advanceToken();
        return $return;
    }
    
    
    /**
     * @return Variable
     * @throws SINQLException
     */
    private function variable()
    {
        $this->expected(Token::TXT_SYMBOL);
        $this->advanceToken();
        $symbol = $this->lexer->symbol();
    
        if (is_null($this->variableMapper)) {
            $message = sprintf("Unable to locate variable mapper. '%s' could not be mapped.", $symbol);
            throw new SINQLException($message);
        }
        
        $value = $this->variableMapper->map($symbol);
        return new Variable($value);
    }
    
    private function operator()
    {
        
    }
    
    private function sequence()
    {
        
    }
    
    private function expected($token)
    {
        if (!$this->matches($token))
            throw new TokenMismatchException($token, $this->nextToken(), $this->lexer->lineColumn());
        return true;
    }
    
    private function matches($token)
    {
        return ($token == $this->nextToken());
    }
    
    private function currentTokenMatches($token)
    {
        return ($this->currentToken() == $token);
    }
    
    private function advanceToken()
    {
        $this->currentToken = $this->nextToken;
        $this->nextToken = $this->lexer->getToken();
    }
    
    
    private function currentToken()
    {
        return $this->currentToken;
    }
    
    private function nextToken()
    {
        return $this->nextToken;
    }
    
    private function isEOF()
    {
        return ($this->currentToken() == Token::EOF);
    }
}
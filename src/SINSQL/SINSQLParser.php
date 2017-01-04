<?php

namespace SINSQL;

use SINSQL\Exceptions\FailedToParseException;
use SINSQL\Exceptions\SINQLException;
use SINSQL\Exceptions\TokenMismatchException;
use SINSQL\Interfaces\IBuffer;
use SINSQL\Interfaces\ITerm;
use SINSQL\Interfaces\IVariableMapper;

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
    
    /**
     * @var ITerm
     */
    private $parseTree;
    
    private $nextToken = Token::EOF;
    private $currentToken = null;
    
    
    public function __construct(IBuffer $buffer, IVariableMapper $variableMapper = null)
    {
        $this->lexer = new Lexer($buffer);
        $this->variableMapper = $variableMapper;
        $this->parseTree = null;
    }
    
    
    /**
     * @return bool
     * @throws SINQLException
     */
    public function parse()
    {
        $this->advanceToken();
        
        if ($this->matches(Token::EOF))
            return false;
        
        $this->expression();
        
        if (is_null($this->parseTree))
            return false;
        
        return boolval($this->parseTree->evaluate());
    }
    
    
    private function expression()
    {
        
    }
    
    private function variable()
    {
        
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
            throw new TokenMismatchException($token, $this->nextToken());
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
}
<?php

namespace SINSQL;

use SINSQL\Exceptions\FailedToParseException;
use SINSQL\Exceptions\SINQLException;
use SINSQL\Exceptions\TokenMismatchException;
use SINSQL\Expressions\Expression;
use SINSQL\Expressions\ExpressionRegistry;
use SINSQL\Expressions\ExpressionType;
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
     * @throws FailedToParseException
     */
    private function expression()
    {
        $this->advanceToken();
        
        $left = $this->left();
        $operator = $this->operator();
        $right = $this->right();
        
        $operator->setLeftRight($left, $right);
        return $operator;
    }
    
    
    /**
     * @return ITerm
     */
    private function left()
    {
        if ($this->currentTokenMatches(Token::TXT_LEFTPAREN)) {
            $expression = $this->expression();
            $this->currentTokenExpected(Token::TXT_RIGHTPAREN);
            $this->advanceToken();
            return $expression;
        }
        
        $left = $this->term();
        $this->expected(Token::TXT_SYMBOL);
        return $left;
    }
    
    
    /**
     * @return Expression
     * @throws FailedToParseException
     * @throws TokenMismatchException
     */
    private function operator()
    {
        $operator = "";
        $currentToken = null;
        $expression = null;
        $lineColumn = $this->lexer->lineColumn();
    
        while ($this->currentTokenMatches(Token::TXT_SPACE) || $this->currentTokenMatches(Token::TXT_SYMBOL)) {
            if ($this->currentTokenMatches(Token::TXT_SPACE)) {
                $representation = null;
                Token::getToken(Token::TXT_SPACE, $representation);
                $operator .= $representation;
            } else {
                $operator .= $this->lexer->symbol();
            }
            
            $this->advanceToken();
        }
         
        $operator = trim($operator, " ");
    
        if ($this->isEOF()) {
            throw new FailedToParseException("Unexpected end to expression", $this->lexer->lineColumn());
        }
        
        if (!ExpressionType::getExpression($operator, $expression)) {
            $message = sprintf("Unable to locate '%s' operator", $operator);
            throw new FailedToParseException($message, $lineColumn);
        }
        
        $expression = ExpressionRegistry::getExpression($expression);
        
        return $expression;
    }
    
    
    /**
     * @return ITerm
     */
    private function right()
    {
        if ($this->currentTokenMatches(Token::TXT_LEFTPAREN)) {
            $expression = $this->expression();
            $this->currentTokenExpected(Token::TXT_RIGHTPAREN);
            $this->advanceToken();
            return $expression;
        }

//        if ($this->isTerm()) {
            $right = $this->term();
//        } else if ($this->isSequence()) {
//
//        }
        
        return $right;
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
    
    private function sequence()
    {
        
    }
    
    private function expected($token)
    {
        if (!$this->matches($token))
            throw new TokenMismatchException($token, $this->nextToken(), $this->lexer->lineColumn());
        return true;
    }
    
    private function currentTokenExpected($token)
    {
        if (!$this->currentTokenMatches($token))
            throw new TokenMismatchException($token, $this->currentToken(), $this->lexer->lineColumn());
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
    
    private function isTerm()
    {
        return (
            $this->currentTokenMatches(Token::TXT_COLON) ||
            $this->currentTokenMatches(Token::TXT_NUMBER) ||
            $this->currentTokenMatches(Token::TXT_STRING)
        );
    }
}
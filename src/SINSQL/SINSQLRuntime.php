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

class SINSQLRuntime
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
    private $token = null;
    
    
    public function __construct(IBuffer& $buffer, IVariableMapper& $variableMapper = null)
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
        if ($this->nextTokenMatches(Token::EOF))
            return null;
        
        return $this->expression();
    }
    
    
    /**
     * @return ITerm
     * @throws FailedToParseException
     */
    private function &expression()
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
        if ($this->matches(Token::TXT_LEFTPAREN)) {
            $expression = $this->expression()->evaluate();
            $this->nextTokenExpected(Token::TXT_RIGHTPAREN);
            $this->advanceToken();
            return new Variable($expression);
        }
        
        return $this->term();
    }
    
    
    /**
     * @return Expression
     * @throws FailedToParseException
     * @throws TokenMismatchException
     */
    private function operator()
    {
        $this->advanceToken();
        $operator = "";
        $expression = null;
        $lineColumn = $this->lexer->lineColumn();
    
        do {
            $operator .= ($this->matches(Token::TXT_SPACE)) ? " " : $this->lexer->symbol();
            $this->advanceToken();
        } while ($this->matches(Token::TXT_SPACE) || $this->matches(Token::TXT_SYMBOL));
        
        $operator = trim($operator, " ");
    
        if ($this->isEOF()) {
            throw new FailedToParseException("Unexpected end to expression", $this->lexer->lineColumn());
        }
        
        if (!ExpressionType::getExpression($operator, $expression)) {
            $message = sprintf("Unable to locate '%s' operator", $operator);
            throw new FailedToParseException($message, $lineColumn);
        }
        
        return ExpressionRegistry::getExpression($expression);
    }
    
    
    /**
     * @return ITerm
     */
    private function right()
    {
        if ($this->matches(Token::TXT_LEFTPAREN)) {
            $expression = $this->expression()->evaluate();
            $this->nextTokenExpected(Token::TXT_RIGHTPAREN);
            $this->advanceToken();
            return new Variable($expression);
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
    private function &term()
    {
        $return = null;
        if ($this->matches(Token::TXT_COLON)) {
            $return = $this->variable();
        }
        
        if ($this->matches(Token::TXT_NUMBER)) {
            // TODO: Change to a different type.
            $return = new Variable($this->lexer->number());
        }
    
        if ($this->matches(Token::TXT_STRING)) {
            $return = new Variable($this->lexer->string());
        }
        
        if (is_null($return)) {
            throw new SINQLException("Invalid call to function " . __CLASS__ . "::term().");
        }
        
        return $return;
    }
    
    
    /**
     * @return Variable
     * @throws SINQLException
     */
    private function &variable()
    {
        $this->nextTokenExpected(Token::TXT_SYMBOL);
        $this->advanceToken();
        $symbol = $this->lexer->symbol();
    
        if (is_null($this->variableMapper)) {
            $message = sprintf("Unable to locate variable mapper. '%s' could not be mapped.", $symbol);
            throw new SINQLException($message);
        }
        
        return new Variable($this->variableMapper->map($symbol));
    }
    
    private function sequence()
    {
        
    }
    
    private function nextTokenExpected($token)
    {
        if (!$this->nextTokenMatches($token))
            throw new TokenMismatchException($token, $this->nextToken, $this->lexer->lineColumn());
        return true;
    }
    
    private function expected($token)
    {
        if (!$this->matches($token))
            throw new TokenMismatchException($token, $this->token, $this->lexer->lineColumn());
        return true;
    }
    
    private function nextTokenMatches($token)
    {
        return ($token == $this->nextToken);
    }
    
    private function matches($token)
    {
        return ($this->token == $token);
    }
    
    private function advanceToken()
    {
        $this->token = $this->nextToken;
        $this->nextToken = $this->lexer->getToken();
    }
    
    private function isEOF()
    {
        return ($this->token == Token::EOF);
    }
    
    private function isTerm()
    {
        return (
            $this->matches(Token::TXT_COLON) ||
            $this->matches(Token::TXT_NUMBER) ||
            $this->matches(Token::TXT_STRING)
        );
    }
}
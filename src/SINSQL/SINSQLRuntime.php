<?php

namespace SINSQL;

use SINSQL\Comparers\StringComparer;
use SINSQL\Exceptions\FailedToParseException;
use SINSQL\Exceptions\SINQLException;
use SINSQL\Exceptions\TokenMismatchException;
use SINSQL\Expressions\ComparableExpression;
use SINSQL\Expressions\Expression;
use SINSQL\Expressions\ExpressionRegistry;
use SINSQL\Expressions\ExpressionType;
use SINSQL\Interfaces\IBuffer;
use SINSQL\Interfaces\ITerm;
use SINSQL\Interfaces\IVariableMapper;
use SINSQL\Operands\ArrayValue;
use SINSQL\Operands\MixedValue;
use SINSQL\Operands\StringValue;

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
    
    private $nextToken = Token::TOK_EOF;
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
        if ($this->nextTokenMatches(Token::TOK_EOF))
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
        
        // Checking for string values
        if (
            $left instanceof StringValue &&
            $right instanceof StringValue &&
            $operator instanceof ComparableExpression
        ) {
            $operator->setComparer(new StringComparer());
        }
        
        $operator->setLeftRight($left, $right);
        return $operator;
    }
    
    
    /**
     * @return ITerm
     */
    private function left()
    {
        if ($this->matches(Token::TOK_LEFTPAREN)) {
            $expression = $this->expression()->evaluate();
            $this->nextTokenExpected(Token::TOK_RIGHTPAREN);
            $this->advanceToken();
            return new MixedValue($expression);
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
            $operator .= ($this->matches(Token::TOK_SPACE)) ? " " : $this->lexer->symbol();
            $this->advanceToken();
        } while ($this->matches(Token::TOK_SPACE) || $this->matches(Token::TOK_SYMBOL));
        
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
        if ($this->matches(Token::TOK_LEFTPAREN)) {
            $expression = $this->expression()->evaluate();
            $this->nextTokenExpected(Token::TOK_RIGHTPAREN);
            $this->advanceToken();
            return new MixedValue($expression);
        }
        
        if ($this->isSequence()) {
            $expression = $this->sequence();
            $this->advanceToken();
            return $expression;
        }
        
        return $this->term();
    }
    
    /**
     * @return ITerm
     * @throws SINQLException
     */
    private function &term()
    {
        $return = null;
        if ($this->matches(Token::TOK_VARIABLE)) {
            $return = $this->variable();
        }
        
        if ($this->matches(Token::TOK_NUMBER)) {
            // TODO: Change to a different type.
            $return = new MixedValue($this->lexer->number());
        }
    
        if ($this->matches(Token::TOK_STRING)) {
            $return = new StringValue($this->lexer->string());
        }
        
        if (is_null($return)) {
            throw new TokenMismatchException([
                Token::TOK_COLON,
                Token::TOK_NUMBER,
                Token::TOK_STRING
            ], $this->token, $this->lexer->lineColumn());
        }
        
        return $return;
    }
    
    
    /**
     * @return ITerm
     * @throws SINQLException
     */
    private function variable()
    {
        $this->expected(Token::TOK_VARIABLE);
        $this->advanceToken();
        $symbol = $this->lexer->symbol();
    
        if (is_null($this->variableMapper)) {
            $message = sprintf("Unable to locate variable mapper. '%s' could not be mapped.", $symbol);
            throw new SINQLException($message);
        }
        
        $mappedVariable = $this->variableMapper->map($symbol);
        if (is_string($mappedVariable))
            return new StringValue($mappedVariable);
        
        return new MixedValue($mappedVariable);
    }
    
    
    /**
     * @return ITerm
     */
    private function &sequence()
    {
        $sequence = new ArrayValue();
        $this->expected(Token::TOK_LEFTBRACK);
        
        do {
            $this->advanceToken();
            
            $sequence[] = $this->term()->evaluate();
            
            // Verify comma rule
            if ($this->nextToken != Token::TOK_RIGHTBRACK && $this->nextToken != Token::TOK_EOF) {
                // Ignore space
                if ($this->nextTokenMatches(Token::TOK_SPACE))
                    $this->advanceToken();
                
                // Must verify comma is present
                $this->nextTokenExpected(Token::TOK_COMMA);
                $this->advanceToken();
                
                // Ignore space
                if ($this->nextTokenMatches(Token::TOK_SPACE))
                    $this->advanceToken();
            }
            
        } while ($this->nextToken != Token::TOK_RIGHTBRACK && $this->nextToken != Token::TOK_EOF);
        
        $this->nextTokenExpected(Token::TOK_RIGHTBRACK);
        
        return $sequence;
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
        return ($this->token == Token::TOK_EOF);
    }
    
    private function isTerm()
    {
        return (
	        $this->matches(Token::TOK_COLON) ||
	        $this->matches(Token::TOK_NUMBER) ||
	        $this->matches(Token::TOK_STRING)
        );
    }
    
    private function isSequence()
    {
        return $this->matches(Token::TOK_LEFTBRACK);
    }
}
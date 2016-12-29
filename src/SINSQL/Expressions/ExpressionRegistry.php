<?php

namespace SINSQL\Expressions;


use SINSQL\Interfaces\IComparer;
use SINSQL\Token;

class ExpressionRegistry
{
    private static $expressionRegistry = null;
    private static $comparableExpressionRegistry = null;
    
    
    /**
     * @param $token
     * @return Expression
     */
    public static function getExpression($token)
    {
        self::checkForExpression($token);
        return new self::$expressionRegistry[$token]();
    }
    
    
    /**
     * @param $token
     * @param IComparer $comparer
     * @return ComparableExpression
     */
    public static function getComparableExpression($token, IComparer $comparer)
    {
        self::checkForExpression($token);
        return new self::$comparableExpressionRegistry[$token]($comparer);
    }
    
    
    private static function checkForExpression($token)
    {
        self::buildRegistry();
        if (!isset(self::$expressionRegistry[$token]) || !class_exists(self::$expressionRegistry[$token])) {
            $prettyToken = null;
            if (!Token::parseToken($token, $prettyToken)) {
                throw new \UnexpectedValueException('Token ' . $token . ' does not exist.');
            }
        
            throw new \UnexpectedValueException('Expression Registry has no entry for "' . $prettyToken . '"');
        }
    }
    
    private static function buildRegistry()
    {
        if (!(is_null(self::$expressionRegistry) || is_null(self::$comparableExpressionRegistry)))
            return;
        
        self::$comparableExpressionRegistry = [
            Token::EXP_EQUALS => '\\SINSQL\\Expressions\\IsExpression',
            Token::EXP_NOTEQUALS => '\\SINSQL\\Expressions\\IsNotExpression',
            Token::EXP_GREATERTHAN => '\\SINSQL\\Expressions\\GreaterThanExpression',
            Token::EXP_GREATERTHANEQUALS => '\\SINSQL\\Expressions\\GreaterThanOrIsExpression',
            Token::EXP_LESSTHAN => '\\SINSQL\\Expressions\\LesserThanExpression',
            Token::EXP_LESSTHANEQUALS => '\\SINSQL\\Expressions\\LesserThanOrIsExpression'
        ];
        
        self::$expressionRegistry = [
            Token::EXP_SEQUENCE => '\\SINSQL\\Expressions\\InExpression',
            Token::EXP_NOTSEQUENCE => '\\SINSQL\\Expressions\\NotInExpression',
            Token::EXP_AND => '\\SINSQL\\Expressions\\AndExpression',
            Token::EXP_OR => '\\SINSQL\\Expressions\\OrExpression'
        ] + self::$comparableExpressionRegistry;
    }
}
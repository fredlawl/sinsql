<?php

namespace SINSQL\Expressions;


use SINSQL\Interfaces\IComparer;

class ExpressionRegistry
{
    private static $expressionRegistry = null;
    private static $comparableExpressionRegistry = null;
    private static $validExpressions = [];
    
    
    /**
     * @param $expression
     * @return Expression
     */
    public static function getExpression($expression)
    {
        self::checkForExpression($expression);
        return new self::$expressionRegistry[$expression]();
    }
    
    
    /**
     * @param $expression
     * @param IComparer $comparer
     * @return ComparableExpression
     */
    public static function getComparableExpression($expression, IComparer $comparer)
    {
        self::checkForExpression($expression);
        return new self::$comparableExpressionRegistry[$expression]($comparer);
    }
    
    
    private static function checkForExpression($expression)
    {
        if (isset(self::$validExpressions[$expression]))
            return;
        
        self::buildRegistry();
        if (!isset(self::$expressionRegistry[$expression]) || !class_exists(self::$expressionRegistry[$expression])) {
            $prettyToken = null;
            if (!ExpressionType::parseExpression($expression, $prettyToken)) {
                throw new \UnexpectedValueException('Token ' . $expression . ' does not exist.');
            }
        
            throw new \UnexpectedValueException('Expression Registry has no entry for "' . $prettyToken . '"');
        }
    
        self::$validExpressions[$expression] = true;
    }
    
    private static function buildRegistry()
    {
        if (!(is_null(self::$expressionRegistry) || is_null(self::$comparableExpressionRegistry)))
            return;
        
        self::$comparableExpressionRegistry = [
            ExpressionType::EXP_EQUALS => '\\SINSQL\\Expressions\\IsExpression',
            ExpressionType::EXP_NOTEQUALS => '\\SINSQL\\Expressions\\IsNotExpression',
            ExpressionType::EXP_GREATERTHAN => '\\SINSQL\\Expressions\\GreaterThanExpression',
            ExpressionType::EXP_GREATERTHANEQUALS => '\\SINSQL\\Expressions\\GreaterThanOrIsExpression',
            ExpressionType::EXP_LESSTHAN => '\\SINSQL\\Expressions\\LesserThanExpression',
            ExpressionType::EXP_LESSTHANEQUALS => '\\SINSQL\\Expressions\\LesserThanOrIsExpression'
        ];
        
        self::$expressionRegistry = [
            ExpressionType::EXP_SEQUENCE => '\\SINSQL\\Expressions\\InExpression',
            ExpressionType::EXP_NOTSEQUENCE => '\\SINSQL\\Expressions\\NotInExpression',
            ExpressionType::EXP_AND => '\\SINSQL\\Expressions\\AndExpression',
            ExpressionType::EXP_OR => '\\SINSQL\\Expressions\\OrExpression'
        ] + self::$comparableExpressionRegistry;
    }
}
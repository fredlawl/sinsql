<?php
/**
 * Created by PhpStorm.
 * User: fredlawl
 * Date: 1/3/17
 * Time: 5:06 PM
 */

namespace SINSQL\Expressions;


class ExpressionType
{
    const EXP_OR = 'OR';
    const EXP_AND = 'AND';
    const EXP_SEQUENCE = 'IN';
    const EXP_NOTSEQUENCE = 'NOT IN';
    const EXP_EQUALS = 'IS';
    const EXP_NOTEQUALS = 'IS NOT';
    const EXP_LESSTHAN = 'LESS THAN';
    const EXP_LESSTHANEQUALS = 'LESS THAN OR IS';
    const EXP_GREATERTHAN = 'GREATER THAN';
    const EXP_GREATERTHANEQUALS = 'GREATER THAN OR IS';
    
    private static $expressionTypesTable = null;
    private static $expressionTypesNamesTable = null;
    
    public static function getExpression($parsedExpression, &$out)
    {
        self::buildExpressionTypeTables();
        if (!isset(self::$expressionTypesNamesTable[$parsedExpression]))
            return false;
    
        $out = constant(__CLASS__ . '::' . self::$expressionTypesNamesTable[$parsedExpression]);
        return true;
    }
    
    public static function parseExpression($expression, &$out)
    {
        self::buildExpressionTypeTables();
        $exp = self::stringify($expression);
        if (!isset(self::$expressionTypesTable[$exp]))
            return false;
    
        $out = self::$expressionTypesTable[$exp];
        return true;
    }
    
    public static function stringify($expression)
    {
        self::buildExpressionTypeTables();
        if (!isset(self::$expressionTypesNamesTable[$expression]))
            return null;
    
        return self::$expressionTypesNamesTable[$expression];
    }
    
    private static function buildExpressionTypeTables()
    {
        if (!(is_null(self::$expressionTypesTable) || is_null(self::$expressionTypesNamesTable)))
            return;
    
        $class = new \ReflectionClass(__CLASS__);
        self::$expressionTypesTable = $class->getConstants();
        self::$expressionTypesNamesTable = array_flip(self::$expressionTypesTable);
    }
}
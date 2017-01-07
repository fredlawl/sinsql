<?php

require_once('../vendor/autoload.php');

use SINSQL\Comparers\StringComparer;
use SINSQL\Expressions\ExpressionType;
use SINSQL\Operands\StringValue;
use SINSQL\Operands\MixedValue;
use SINSQL\Expressions\ExpressionRegistry;
use SINSQL\Token;

class ParseTreeTest extends PHPUnit_Framework_TestCase
{
    public function testStringEqualsOperation()
    {
        $tree = ExpressionRegistry::getComparableExpression(ExpressionType::EXP_EQUALS, new StringComparer());
        
        $tree->setLeftRight(new StringValue("test"), new StringValue("test"));
        $this->assertTrue($tree->evaluate());

        $tree->setLeftRight(new StringValue("test"), new StringValue("test1"));
        $this->assertFalse($tree->evaluate());
    }
    
    public function testNumberEqualsOperation()
    {
        $tree = ExpressionRegistry::getExpression(ExpressionType::EXP_EQUALS);
        
        $tree->setLeftRight(new MixedValue(2), new MixedValue(3));
        $this->assertFalse($tree->evaluate());
        
        $tree->setLeftRight(new MixedValue(2), new MixedValue(2));
        $this->assertTrue($tree->evaluate());
    }
    
    public function testComplexTreeAnd()
    {
        $tree = ExpressionRegistry::getExpression(ExpressionType::EXP_AND);
        
        $numEqualOperation = ExpressionRegistry::getExpression(ExpressionType::EXP_EQUALS);
        $numEqualOperation->setLeftRight(new MixedValue(2), new MixedValue(2));
        
        $strEqualOperation = ExpressionRegistry::getComparableExpression(ExpressionType::EXP_EQUALS, new StringComparer());
        $strEqualOperation->setLeftRight(new StringValue("test"), new StringValue("test"));
        
        $tree->setLeftRight($numEqualOperation, $strEqualOperation);
        
        $this->assertTrue($tree->evaluate());
    }
    
    public function testComplexTreeOr()
    {
        $tree = ExpressionRegistry::getExpression(ExpressionType::EXP_OR);
    
        $numEqualOperation = ExpressionRegistry::getExpression(ExpressionType::EXP_EQUALS);
        $numEqualOperation->setLeftRight(new MixedValue(2), new MixedValue(4));
    
        $strEqualOperation = ExpressionRegistry::getComparableExpression(ExpressionType::EXP_EQUALS, new StringComparer());
        $strEqualOperation->setLeftRight(new StringValue("TEST"), new StringValue("test"));
    
        $tree->setLeftRight($numEqualOperation, $strEqualOperation);
    
        $this->assertTrue($tree->evaluate());
    }
    
    public function testNonsensicalTreeAnd()
    {
        $tree = ExpressionRegistry::getExpression(ExpressionType::EXP_AND);
        
        // This should evaluate to true because 1 > 0 => true, and filled string set to "true" boolvals to true
        // (true && true)
        $tree->setLeftRight(new MixedValue(1), new StringValue("true"));
        $this->assertTrue($tree->evaluate());
    
        // This should evaluate to false because a 0 => false, and filled string set to "false" boolvals to false
        // (false && false)
        $tree->setLeftRight(new MixedValue(0), new StringValue("false"));
        $this->assertFalse($tree->evaluate());
    
        // This should evaluate to true because a 5 > 0 => true, and filled string set to "anything" boolvals to true
        // (true && true)
        $tree->setLeftRight(new MixedValue(5), new StringValue("something"));
        $this->assertTrue($tree->evaluate());
    
        // This should evaluate to true because a generic object boolvals to true, and filled string set to "anything" boolvals to true
        // (true && true)
        $tree->setLeftRight(new MixedValue((object) []), new StringValue("something"));
        $this->assertTrue($tree->evaluate());
    }
}

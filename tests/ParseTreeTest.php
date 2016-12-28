<?php

require_once('../vendor/autoload.php');

use SINSQL\Comparers\StringComparer;
use SINSQL\Operands\Str;
use SINSQL\Operands\Variable;
use SINSQL\Operations\OperationRegistry;
use SINSQL\Token;

class ParseTreeTest extends PHPUnit_Framework_TestCase
{
    public function testStringEqualsOperation()
    {
        $tree = OperationRegistry::getOperation(Token::LOGICEQ);
        $tree->setComparer(new StringComparer());
        
        $tree->setLeftRight(new Str("test"), new Str("test"));
        $this->assertTrue($tree->evaluate());

        $tree->setLeftRight(new Str("test"), new Str("test1"));
        $this->assertFalse($tree->evaluate());
    }
    
    public function testNumberEqualsOperation()
    {
        $tree = OperationRegistry::getOperation(Token::LOGICEQ);
        
        $tree->setLeftRight(new Variable(2), new Variable(3));
        $this->assertFalse($tree->evaluate());
        
        $tree->setLeftRight(new Variable(2), new Variable(2));
        $this->assertTrue($tree->evaluate());
    }
    
    public function testComplexTreeAnd()
    {
        $tree = OperationRegistry::getOperation(Token::LOGICAND);
        
        $numEqualOperation = OperationRegistry::getOperation(Token::LOGICEQ);
        $numEqualOperation->setLeftRight(new Variable(2), new Variable(2));
        
        $strEqualOperation = OperationRegistry::getOperation(Token::LOGICEQ);
        $strEqualOperation->setComparer(new StringComparer());
        $strEqualOperation->setLeftRight(new Str("test"), new Str("test"));
        
        $tree->setLeftRight($numEqualOperation, $strEqualOperation);
        
        $this->assertTrue($tree->evaluate());
    }
    
    public function testComplexTreeOr()
    {
        $tree = OperationRegistry::getOperation(Token::LOGICOR);
    
        $numEqualOperation = OperationRegistry::getOperation(Token::LOGICEQ);
        $numEqualOperation->setLeftRight(new Variable(2), new Variable(4));
    
        $strEqualOperation = OperationRegistry::getOperation(Token::LOGICEQ);
        $strEqualOperation->setComparer(new StringComparer());
        $strEqualOperation->setLeftRight(new Str("TEST"), new Str("test"));
    
        $tree->setLeftRight($numEqualOperation, $strEqualOperation);
    
        $this->assertTrue($tree->evaluate());
    }
    
    public function testNonsensicalTreeAnd()
    {
        $tree = OperationRegistry::getOperation(Token::LOGICAND);
        
        // This should evaluate to true because 1 > 0 => true, and filled string set to "true" boolvals to true
        // (true && true)
        $tree->setLeftRight(new Variable(1), new Str("true"));
        $this->assertTrue($tree->evaluate());
    
        // This should evaluate to false because a 0 => false, and filled string set to "false" boolvals to false
        // (false && false)
        $tree->setLeftRight(new Variable(0), new Str("false"));
        $this->assertFalse($tree->evaluate());
    
        // This should evaluate to true because a 5 > 0 => true, and filled string set to "anything" boolvals to true
        // (true && true)
        $tree->setLeftRight(new Variable(5), new Str("something"));
        $this->assertTrue($tree->evaluate());
    
        // This should evaluate to true because a generic object boolvals to true, and filled string set to "anything" boolvals to true
        // (true && true)
        $tree->setLeftRight(new Variable((object) []), new Str("something"));
        $this->assertTrue($tree->evaluate());
    }
}

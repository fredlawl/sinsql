<?php

require_once('../vendor/autoload.php');

use SINSQL\Expressions\ExpressionRegistry;
use SINSQL\Expressions\ExpressionType;

class OperationRegistryTest extends PHPUnit_Framework_TestCase
{
    public function testExpressonRegistryFails()
    {
        try {
            $garbage = ExpressionRegistry::getExpression('asdfasdf');
            $this->assertTrue(false);
        } catch (\UnexpectedValueException $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testExpressionRegistryReturnsAndExpresion()
    {
        $andOperation = ExpressionRegistry::getExpression(ExpressionType::EXP_AND);
        $this->assertEquals("SINSQL\\Expressions\\AndExpression", get_class($andOperation));
    }
}

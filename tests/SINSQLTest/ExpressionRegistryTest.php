<?php

namespace SINSQLTest;

use SINSQL\Expressions\ExpressionRegistry;
use SINSQL\Expressions\ExpressionType;

class ExpressionRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testExpressionRegistryFails()
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

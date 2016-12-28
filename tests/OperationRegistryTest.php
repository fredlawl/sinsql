<?php

require_once('../vendor/autoload.php');

use SINSQL\Operations\OperationRegistry;
use SINSQL\Token;

class OperationRegistryTest extends PHPUnit_Framework_TestCase
{
    public function testOperationRegistryFails()
    {
        try {
            $garbage = OperationRegistry::getOperation('asdfasdf');
            $this->assertTrue(false);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }
    
    public function testOperationFactoryReturnsAndOperation()
    {
        $andOperation = OperationRegistry::getOperation(Token::LOGICAND);
        $this->assertEquals("SINSQL\\Operations\\AndOperation", get_class($andOperation));
    }
}

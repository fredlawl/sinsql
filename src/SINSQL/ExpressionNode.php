<?php

namespace SINSQL;


use SINSQL\Interfaces\IOperand;

abstract class ExpressionNode
{
    /**
     * @var IOperand
     */
    protected $left;
    
    /**
     * @var IOperand
     */
    protected $right;
}
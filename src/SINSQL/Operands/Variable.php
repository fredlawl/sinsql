<?php

namespace SINSQL\Operands;


use SINSQL\Interfaces\IOperand;

class Variable implements IOperand
{
    public $value;
    
    public function __construct($value)
    {
        $this->value = $value;
    }
    
    public function evaluate()
    {
        return $this->value;
    }
}
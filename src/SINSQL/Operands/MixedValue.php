<?php

namespace SINSQL\Operands;


use SINSQL\Interfaces\ITerm;

class MixedValue implements ITerm
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
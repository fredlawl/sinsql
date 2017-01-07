<?php

namespace SINSQL\Operands;


use ArrayAccess;
use ArrayObject;
use SINSQL\Interfaces\ITerm;

class ArrayValue extends ArrayObject implements ITerm
{
    public function __construct(array $value = [])
    {
        parent::__construct($value, 0, "ArrayIterator");
    }
    
    public function evaluate()
    {
        return $this->getArrayCopy();
    }
}
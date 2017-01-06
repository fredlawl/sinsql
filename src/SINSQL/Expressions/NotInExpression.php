<?php

namespace SINSQL\Expressions;


use SINSQL\Exceptions\NotImplementedException;

class NotInExpression extends Expression
{
    
    public function evaluate()
    {
        throw new NotImplementedException();
    }
}
<?php

namespace SINSQL\Expressions;


use SINSQL\Exceptions\NotImplementedException;
use SINSQL\Interfaces\ITerm;

class InExpression extends Expression
{
    
    public function evaluate()
    {
        throw new NotImplementedException();
//        $this->checkForNulls();
//        $right = $this->right->evaluate();
//        if (!is_array($right))
//            throw new \InvalidArgumentException("Node right hand side must be an array.");
//
//        return in_array($this->left->evaluate(), $right);
    }
}
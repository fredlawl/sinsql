<?php

namespace SINSQL\Operations;


use SINSQL\Interfaces\IOperand;

class InOperation extends Operation implements IOperand
{
    
    public function evaluate()
    {
        $this->checkForNulls();
        $right = $this->right->evaluate();
        if (!is_array($right))
            throw new \InvalidArgumentException("Node right hand side must be an array.");
        
        return in_array($this->left->evaluate(), $right);
    }
}
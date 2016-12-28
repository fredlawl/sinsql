<?php

namespace SINSQL\Operations;


use SINSQL\Interfaces\IOperand;

class AndOperation extends Operation implements IOperand
{
    
    public function evaluate()
    {
        $this->checkForNulls();
        return boolval($this->left->evaluate()) && boolval($this->right->evaluate());
    }
}
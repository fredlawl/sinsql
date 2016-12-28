<?php

namespace SINSQL\Operations;


use SINSQL\Interfaces\IOperand;

class OrOperation extends Operation implements IOperand
{
    public function evaluate()
    {
        $this->checkForNulls();
        return boolval($this->left->evaluate()) || boolval($this->right->evaluate());
    }
}
<?php

namespace SINSQL\Operations;


use SINSQL\ExpressionNode;
use SINSQL\Interfaces\IOperand;

abstract class Operation extends ExpressionNode
{
    public function isNodeNull()
    {
        return $this->isLeftNodeNull() && $this->isRightNodeNull();
    }
    
    public function isLeftNodeNull()
    {
        return is_null($this->left);
    }
    
    public function isRightNodeNull()
    {
        return is_null($this->right);
    }
    
    protected function checkForNulls()
    {
        if ($this->isNodeNull())
            throw new \Exception("Left and Right is null.");
        
        if ($this->isLeftNodeNull())
            throw new \Exception("Left node is null.");
        
        if ($this->isRightNodeNull())
            throw new \Exception("Right node is null.");
    }
    
    public function setLeft(IOperand $operand)
    {
        $this->left = $operand;
    }
    
    public function setRight(IOperand $operand)
    {
        $this->right = $operand;
    }
    
    public function setLeftRight(IOperand $left, IOperand $right)
    {
        $this->setLeft($left);
        $this->setRight($right);
    }
}
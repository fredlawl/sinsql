<?php

namespace SINSQL\Expressions;


use SINSQL\Exceptions\SINQLException;
use SINSQL\Interfaces\ITerm;

abstract class Expression implements ITerm
{
    /**
     * @var ITerm
     */
    protected $left;
    
    /**
     * @var ITerm
     */
    protected $right;
    
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
            throw new SINQLException("Left and Right of an expression are null.");
        
        if ($this->isLeftNodeNull())
            throw new SINQLException("Left node of an expression is null.");
        
        if ($this->isRightNodeNull())
            throw new SINQLException("Right node of an expression is null.");
    }
    
    public function setLeft(ITerm& $operand)
    {
        $this->left = $operand;
    }
    
    public function setRight(ITerm& $operand)
    {
        $this->right = $operand;
    }
    
    public function setLeftRight(ITerm& $left, ITerm& $right)
    {
        $this->setLeft($left);
        $this->setRight($right);
    }
}
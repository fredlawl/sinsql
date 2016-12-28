<?php

namespace SINSQL\Operations;


use SINSQL\Comparers\DefaultComparer;
use SINSQL\Interfaces\ICanCompare;
use SINSQL\Interfaces\IComparer;
use SINSQL\Interfaces\IOperand;

class EqualOperation extends Operation implements IOperand, ICanCompare
{
    /**
     * @var IComparer
     */
    private $comparer;
    
    public function __construct()
    {
        $this->comparer = new DefaultComparer();
    }
    
    public function setComparer(IComparer $comparison)
    {
        $this->comparer = $comparison;
    }
    
    public function evaluate()
    {
        return ($this->doCompare() == 0);
    }
    
    public function doCompare()
    {
        $this->checkForNulls();
        return $this->comparer->compare($this->left->evaluate(), $this->right->evaluate());
    }
}
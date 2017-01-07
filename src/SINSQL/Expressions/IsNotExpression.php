<?php

namespace SINSQL\Expressions;


use SINSQL\Exceptions\NotImplementedException;
use SINSQL\Interfaces\ICanCompare;
use SINSQL\Interfaces\IComparer;

class IsNotExpression extends ComparableExpression
{
    
    public function __construct(IComparer& $comparer = null)
    {
        parent::__construct($comparer);
    }
    
    public function doCompare()
    {
        $this->checkForNulls();
        if ($this->comparer->compare($this->left->evaluate(), $this->right->evaluate()) == 0)
            return -1;
        return 0;
    }
}
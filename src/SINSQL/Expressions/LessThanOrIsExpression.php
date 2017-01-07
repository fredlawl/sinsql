<?php

namespace SINSQL\Expressions;


use SINSQL\Exceptions\NotImplementedException;
use SINSQL\Interfaces\ICanCompare;
use SINSQL\Interfaces\IComparer;

class LessThanOrIsExpression extends ComparableExpression
{
    
    public function __construct(IComparer& $comparer = null)
    {
        parent::__construct($comparer);
    }
    
    public function doCompare()
    {
        $this->checkForNulls();
        $comparison = $this->comparer->compare($this->left->evaluate(), $this->right->evaluate());
        return ($comparison == 0 || $comparison < 0) ? 0 : 1;
    }
}
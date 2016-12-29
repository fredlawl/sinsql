<?php

namespace SINSQL\Expressions;


use SINSQL\Comparers\DefaultComparer;
use SINSQL\Interfaces\ICanCompare;
use SINSQL\Interfaces\IComparer;

class IsExpression extends ComparableExpression
{
    public function __construct(IComparer $comparer = null)
    {
        parent::__construct($comparer);
    }
    
    public function doCompare()
    {
        $this->checkForNulls();
        return $this->comparer->compare($this->left->evaluate(), $this->right->evaluate());
    }
}
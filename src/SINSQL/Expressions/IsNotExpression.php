<?php

namespace SINSQL\Expressions;


use SINSQL\Interfaces\ICanCompare;
use SINSQL\Interfaces\IComparer;

class IsNotExpression extends ComparableExpression
{
    
    public function __construct(IComparer $comparer = null)
    {
        parent::__construct($comparer);
    }
    
    public function doCompare()
    {
        // TODO: Implement doCompare() method.
    }
}
<?php

namespace SINSQL\Expressions;


use SINSQL\Exceptions\NotImplementedException;
use SINSQL\Interfaces\ICanCompare;
use SINSQL\Interfaces\IComparer;

class LessThanExpression extends ComparableExpression
{
    
    public function __construct(IComparer $comparer = null)
    {
        parent::__construct($comparer);
    }
    
    public function doCompare()
    {
        throw new NotImplementedException();
    }
}
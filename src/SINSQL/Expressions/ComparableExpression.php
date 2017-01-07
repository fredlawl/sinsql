<?php

namespace SINSQL\Expressions;


use SINSQL\Comparers\DefaultComparer;
use SINSQL\Interfaces\ICanCompare;
use SINSQL\Interfaces\IComparer;

abstract class ComparableExpression extends Expression implements ICanCompare
{
    /**
     * @var IComparer
     */
    protected $comparer;
    
    public abstract function doCompare();
    
    public function __construct(IComparer& $comparer = null)
    {
        if (is_null($comparer))
            $comparer = new DefaultComparer();
        $this->setComparer($comparer);
    }
    
    public function setComparer(IComparer& $comparer)
    {
        $this->comparer = $comparer;
    }
    
    public function evaluate()
    {
        return ($this->doCompare() == 0);
    }
}
<?php

namespace SINSQL\Expressions;


use SINSQL\Exceptions\NotImplementedException;
use SINSQL\Exceptions\SINQLException;

class NotInExpression extends Expression
{
    
    public function evaluate()
    {
        $this->checkForNulls();
        $needle = $this->left->evaluate();
        $haystack = $this->right->evaluate();
    
        if (!is_array($haystack)) {
            $message = "Possible grammar rule violation. A 'sequence' can only exists on the right hand side of an expression. Check the documentation for further details.";
            throw new SINQLException($message);
        }
    
        return !in_array($needle, $haystack);
    }
}
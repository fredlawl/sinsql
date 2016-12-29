<?php

namespace SINSQL\Expressions;


class OrExpression extends Expression
{
    public function evaluate()
    {
        $this->checkForNulls();
        return boolval($this->left->evaluate()) || boolval($this->right->evaluate());
    }
}
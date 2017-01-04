<?php
/**
 * Created by PhpStorm.
 * User: fredlawl
 * Date: 1/3/17
 * Time: 11:22 PM
 */

namespace SINSQL\Exceptions;


class UndefinedVariableException extends SINQLException
{
    public function __construct($variable)
    {
        $message = sprintf("Variable '%s' is undefined. Have you mapped it?", $variable);
        parent::__construct($message);
    }
}
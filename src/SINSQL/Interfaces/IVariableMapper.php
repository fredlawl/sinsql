<?php
/**
 * Created by PhpStorm.
 * User: fredlawl
 * Date: 1/3/17
 * Time: 9:10 PM
 */

namespace SINSQL\Interfaces;


interface IVariableMapper
{
    /**
     * @param $variableKey
     * @return mixed; null if variable cannot be mapped.
     */
    public function map($variableKey);
}
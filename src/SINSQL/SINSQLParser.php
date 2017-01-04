<?php

namespace SINSQL;

use SINSQL\Interfaces\IBuffer;
use SINSQL\Interfaces\ITerm;
use SINSQL\Interfaces\IVariableMapper;

class SINSQLParser
{
    /**
     * @var Lexer
     */
    private $scanner;
    
    /**
     * @var IVariableMapper
     */
    private $variableMapper;
    
    /**
     * @var ITerm
     */
    private $parseTree;
    
    public function __construct(IBuffer $buffer, IVariableMapper $variableMapper)
    {
        $this->scanner = new Lexer($buffer);
        $this->variableMapper = $variableMapper;
        $this->parseTree = null;
    }
    
    
    /**
     * @return ITerm
     */
    public function parse()
    {
        return $this->parseTree;
    }
    
    
    private function expression()
    {
        
    }
    
    
}
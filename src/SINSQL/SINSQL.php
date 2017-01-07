<?php

namespace SINSQL;


use SINSQL\Exceptions\NotImplementedException;
use SINSQL\Interfaces\IVariableMapper;

class SINSQL
{
    /**
     * @var IVariableMapper
     */
    private $variableMapper;
    
    
    /**
     * SINSQL constructor.
     * @param IVariableMapper $variableMapper
     */
    public function __construct(IVariableMapper& $variableMapper = null)
    {
        $this->variableMapper = $variableMapper;
    }
    
    
    /**
     * @param $query
     * @return bool
     */
    public function parse($query)
    {
        $runtime = new SINSQLRuntime(new StringBuffer($query), $this->variableMapper);
        return $runtime->parse();
    }
    
    
    public function parseFile($filename)
    {
        throw new NotImplementedException();
    }
}
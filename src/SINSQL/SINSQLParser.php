<?php

namespace SINSQL;

class SINSQLParser
{
    private $cursor;
    private $input;
    private $currentSymbol;
    private $previousSymbol;
    private $tree;
    
    public function __construct()
    {
        $this->tree = null;
    }
    
    public function run($input)
    {
        $this->input = $input;
        $this->cursor = 0;
        $this->expression();
        
//        $buffer = "";
//        $count = strlen($this->input);
//        self::$cursor = 0;
//        while (self::$cursor < $count) {
//            if ($this->isWhitespace($this->input[self::$cursor])) {
//                ++self::$cursor;
//                continue;
//            }
//
//            // consume
//            $buffer .= $this->input[self::$cursor];
//            $this->consume($buffer);
//            ++self::$cursor;
//        }
//
//        return $buffer;
        
    }
    
    private function expression()
    {
        // TODO: Implement parseExpression() method.
    }
    
    
}
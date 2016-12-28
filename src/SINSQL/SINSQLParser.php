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
    
    // expression = ["("] ( operand | expression ), operator, ( operand | expression | sequence ) [")"];
    // operator = "AND" | "OR" | "IS" | "NOT" | "IN" | "LESS THAN" | "GREATER THAN" | "LESS THAN OR IS" | "GREATER THAN OR IS";
    // operand = { term | number };
    // sequence = "(", { term, "," }, -",", ")"
    // term = number | string | variable ;
    // string = """, { ( letter | symbol | number ) - """ }, """;
    // variable = ":", { letter };
    // letter = [a-zA-Z];
    // symbol = ? anything not a letter but is considered (special?) character ?
    // number = [0-9];
    
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
    
    private function isWhitespace($characters)
    {
        return preg_match("/\s/i", $characters) > 0;
    }
    
    
}
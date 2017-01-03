# SINSQL (SINSQL is not SQL)

> DISCLAIMER: Still in active development

A small little language interpreter written in PHP to parse and execute user-defined logic.

This pet project was inspired by a past project where a grammar similar to this was needed to conditionally render content on site based on certain user-defined criteria.

## Examples
```
"text" IS "TEXT"
=> true
```
```
"Chiefs" IN ("L.A. Rams", "St. Louis Rams", "Rams")
 => false
``` 
```
25 GREATER THAN OR IS 21 OR 1991 LESS THAN OR IS 1995
 => true
```
 ```
 :isAwesome IS true OR (:age GREATER THAN OR IS 21 AND (:footballteam IN ("L.A. Rams", "St. Louis Rams", "Rams")))
 => true
 ``` 

## Grammar Definition
```
expression = ["("], left, operator, right, [")"];
left = term | expression;
right = term | expression | sequence;
operator = "AND" | "OR" | "IS" | "IS NOT" | "IN" | "NOT IN" | "LESS THAN" | "GREATER THAN" | "LESS THAN OR IS" | "GREATER THAN OR IS";
sequence = "(", { ( term, "," ) }, -",", ")"
term = number | string | variable;
string = '"', { ( letter | symbol | number ) -'"' }, '"';
variable = ":", { letter };
letter = [a-zA-Z];
symbol = ? anything not a letter but is considered (special?) character ?
number = { [0-9] };
```

## License
Copyright 2016 Frederick Lawler

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
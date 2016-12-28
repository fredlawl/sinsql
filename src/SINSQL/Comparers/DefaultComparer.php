<?php

namespace SINSQL\Comparers;


use SINSQL\Interfaces\IComparer;

class DefaultComparer implements IComparer
{
    public function compare($a, $b)
    {
        if ($a == $b)
            return 0;
        
        return ($a > $b) ? 1 : -1;
    }
}
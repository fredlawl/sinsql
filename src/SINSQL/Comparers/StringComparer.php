<?php

namespace SINSQL\Comparers;


use SINSQL\Interfaces\IComparer;

class StringComparer implements IComparer
{
    public function compare($a, $b)
    {
        return strcasecmp($a, $b);
    }
}
<?php

namespace SINSQL\Interfaces;


interface ICanCompare
{
    public function setComparer(IComparer $comparer);
    public function doCompare();
}
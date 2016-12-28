<?php

namespace SINSQL;


class Token
{
    const LPARANN = '(';
    const RPARAN = ')';
    
    const LOGICOR = 'OR';
    const LOGICAND = 'AND';
    const LOGCICIN = 'IN';
    const LOGICNOT = 'NOT';
    const LOGICEQ = 'IS';
    
    const CMPLESSTHAN = 'LESS THAN';
    const CMPLESSTHANEQ = 'LESS THAN OR IS';
    
    const CMPGREATERTHAN = 'GREATER THAN';
    const CMPGREATERTHANEQ = 'GREATER THAN OR IS';
}
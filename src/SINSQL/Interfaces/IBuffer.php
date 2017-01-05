<?php
/**
 * Created by PhpStorm.
 * User: fredlawl
 * Date: 1/2/17
 * Time: 9:18 PM
 */

namespace SINSQL\Interfaces;


interface IBuffer
{
    public function get();
    public function reset();
    public function currentLine();
    public function currentColumn();
    public function displayLineColumn();
    public function isEOF();
}
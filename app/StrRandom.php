<?php

namespace App;

class StrRandom
{
    public function random(int $len)
    {
        return \Str::random($len);
    }
}
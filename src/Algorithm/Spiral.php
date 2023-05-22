<?php

namespace Maalls\Chart\Algorithm;

class Spiral implements Func {
    public function map($x)
    {
        return [cos($x*16*pi()), sin($x*16*pi())];
    }

}
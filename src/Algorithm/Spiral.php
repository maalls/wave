<?php

namespace Maalls\Chart\Algorithm;

class Spiral implements Func {
    public function map($x)
    {
        return [cos($x*2*pi()), sin($x*2*pi())];
    }

}
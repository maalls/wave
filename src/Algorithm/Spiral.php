<?php

namespace Maalls\Chart\Algorithm;

class Spiral implements Func {
    public function map($x)
    {
        return [sin($x*pi()), sin($x*pi())];
    }

}
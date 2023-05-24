<?php

namespace Maalls\Chart\Algorithm;

class Spiral implements Func {
    public function map($x)
    {
        return [cos($x*pi()), sin($x*pi())];
    }

}
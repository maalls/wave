<?php

namespace Maalls\Chart\Algorithm;

class Spiral implements Func {
    public function map($x)
    {
        return [0, sin($x*pi())];
    }

}
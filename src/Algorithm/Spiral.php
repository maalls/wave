<?php

namespace Maalls\Chart\Algorithm;

class Spiral implements Func {
    public function map($x)
    {
        return [0,cos($x*8*pi())];
    }

}
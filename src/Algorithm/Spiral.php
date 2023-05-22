<?php

namespace Maalls\Chart\Algorithm;
use Maalls\Chart\Tool\Color;
class Spiral {
    public function map($x)
    {
        return [cos($x*2*pi())];
    }

}
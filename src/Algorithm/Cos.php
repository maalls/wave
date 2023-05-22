<?php

namespace Maalls\Chart\Algorithm;
use Maalls\Chart\Tool\Color;
class Cos {
    public function map($x, $t)
    {
        return [cos($x*2*pi())];
    }

}
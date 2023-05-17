<?php

namespace Maalls\Chart\Algorithm;
use Maalls\Chart\Algorithm;
class Mandelbrot {
    public function map($x, $y)
    {

        $c = [$x, $y];
        $v = [0, 0];

        $reals = [];
        $imaginaries = [];
        $cycle = true;
        
        for ($i = 0; $i < 100; $i++) {

            $t = $v[0] * $v[0] - $v[1] * $v[1] + $c[0];
            $v[1] = 2 * $v[0] * $v[1] + $c[1];
            $v[0] = $t;
            if ($v[0] * $v[0] > 2 || $v[1] * $v[1] > 2) {
                $cycle = false;
                break;
            }

            $index = array_search($v[0], $reals);
            if ($index !== false && $imaginaries[$index] == $v[1]) {

                $cycle = count($reals);
                break;

            }
            $reals[] = $v[0];
            $imaginaries[] = $v[1];


        }
        //echo "($x, $y) cycle $cycle" . PHP_EOL;

        if ($cycle === false) {
            return '#000000';
        } else {
            return '#FFFFFF';
        }
    }


}
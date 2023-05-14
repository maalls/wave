<?php
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart;
use \Maalls\Complex;

$width = 1000;
$height = 1000;
$chart = new Chart($width, $height);
$chart->setRanges(-2,1,-1,1);
$mandelbot = function($x, $y) {

    //$c = new Complex($x, $y);
    //$v = new Complex(0, 0);
    $c = [$x, $y];
    $v = [0,0];

    $reals = [];
    $imaginaries = [];
    $cycle = false;
    for($i = 0; $i < 1000; $i++) {

        /*$v->square()->add($c);

        $index = array_search($v->real, $reals);
        if($index !== false && $imaginaries[$index] == $v->imaginary) {

            $cycle = count($reals);
            break;

        }
        $reals[] = $v->real;
        $imaginaries[] = $v->imaginary;*/

        $t = $v[0]*$v[0] - $v[1]*$v[1] + $c[0];
        $v[1] = 2*$v[0]*$v[1] + $c[1];
        $v[0] = $t;
        if($v[0]*$v[0] > 2 || $v[1] * $v[1] > 2) {
            $cycle = false;
            break;
        }

        $index = array_search($v[0], $reals);
        if($index !== false && $imaginaries[$index] == $v[1]) {

            $cycle = count($reals);
            break;

        }
        $reals[] = $v[0];
        $imaginaries[] = $v[1];


    }
    //echo "($x, $y) cycle $cycle" . PHP_EOL;

    if($cycle === false) {
        return '#000000';
    }
    else {
        return '#FFFFFF';
    }



};

$random = function($x, $y) {
    return rand(0, 1) > 0 ? '#FF0000' : '#0000FF';
};

$chart->paint($mandelbot);
$file = __DIR__ . '/data/mandelbrot.png';
$chart->save($file);
?>
<!DOCTYPE html>
<html>
<body>
<style>
    img {
        width: 400px;
    }
    </style>
<img src="/data/mandelbrot.png" />

</body>
</html>
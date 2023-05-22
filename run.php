<?php

require __DIR__ . '/vendor/autoload.php';
use \Maalls\Chart\Chart;
use \Maalls\Chart\Algorithm\Mandelbrot;
use \Maalls\Chart\Tool\MultiThread;

$width = 300;
$height = 200;

$xMin = -2;
$xMax = 1;
$yMin = -1;
$yMax = 1;

$chart = new Chart($width, $height);
$chart->setRanges($xMin, $xMax, $yMin, $yMax);
$algo = new Mandelbrot();
$chart->add($algo);

$file = __DIR__ . "/toto";

$chart->frameCount = 12;
$chart->framePerSecond = 2;
$chart->zoom2(-0.5, 0.5, -0.5, 0.5, $chart->frameCount / $chart->framePerSecond);
$chart->draw($file);

/*$mt = new MultiThread();

$mt->iterate(function($t) {
    file_put_contents(__DIR__ . '/toto/' . $t . ".txt", '');
}, 0, 100);

*/
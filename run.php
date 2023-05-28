<?php

require __DIR__ . '/vendor/autoload.php';
use \Maalls\Chart\Chart;
use \Maalls\Chart\Algorithm\Mandelbrot;
use \Maalls\Chart\Tool\MultiThread;
use \Maalls\Chart\Algorithm\WaveSurface;

$chart = new Chart(300, 300);
$chart->frameCount = 20;
$chart->framePerSecond = 4;
$chart->setRanges(-1,1,-1,1,-1,1);

$func = new WaveSurface();
$chart->add($func);
$chart->draw(__DIR__ . '/output.png');

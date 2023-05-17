<?php

require __DIR__ . '/vendor/autoload.php';
use \Maalls\Chart\Chart;
use \Maalls\Chart\Algorithm\Xtmap;

$width = 100;
$height = 100;

$xMin = 0;
$xMax = 10;
$yMin = 0;
$yMax = 10;

$chart = new Chart($width, $height);
$chart->setRanges($xMin, $xMax, $yMin, $yMax);
$algo = new Xtmap();
$chart->add($algo);

$file = __DIR__ . "/test.png";

$chart->draw($file);

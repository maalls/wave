<?php
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart;
use \Maalls\Complex;

$width = 500;
$height = 500;
$chart = new Chart($width, $height);
$chart->setRanges(-2, 2, -2, 2);
$chart->paint(function($x, $y) {

});
$file = __DIR__ . '/data/test.png';
$chart->save($file);
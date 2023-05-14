<?php
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart;
use \Maalls\Complex;

$width = 500;
$height = 500;
$chart = new Chart($width, $height);
$chart->setRanges(-255, 255, -255, 255);
$chart->paint(function($x, $y) {

    echo "($x,$y)" . PHP_EOL;
    $r = decToHex($x);
    $v = decToHex($y);
    $hex = "#" . $r . $v . '00';;
    echo $hex . PHP_EOL;
    return $hex;
});
$file = __DIR__ . '/data/test.png';
$chart->save($file);

function decToHex($dec) {
    $v = min(max(0, round($dec/50) * 50), 255);
    $v = str_pad(dechex($v),  2, "0", STR_PAD_LEFT);
    return $v;
}
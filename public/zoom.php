<?php
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart;

$width = 600;
$height = 600;
$chart = new Chart($width, $height);
$chart->setRanges(-4, 4, -4, 4);
//$chart->setUnit(100, 100);
//$chart->setCenter($width/2, $height/2);
$chart->still(1);
$chart->zoom(0, 2, 2, 2);
$chart->still(1);
$chart->zoom(0, -2, 0.5, 2);
$chart->still(2);
//$chart->zoom(0, 0, 10, 4);
//$chart->still(1);
//$chart->pan(2,2, 4);
//$chart->pan(-2,-2, 4);
$chart->framePerSecond = 24;
$chart->frameCount = 24*12;
$chart->printTime = true;
$k = 2 * pi();
$w = 2;

$chart->add(function($x, $t) use($k, $w) {
    return 2 * sin($x) * cos($t * 2 * pi());
}, '#000000');
//$file = __DIR__ . '/data/output.png';
//$chart->png($file);

$file = __DIR__ . '/data/wave.gif';
$chart->draw($file);
?>
<!DOCTYPE html>
<html>
<body>
<style>
    img {
        
    }
    </style>
<img src="/data/wave.gif" />

</body>
</html>
    

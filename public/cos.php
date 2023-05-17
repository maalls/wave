<?php
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart\Chart;

$width = 600;
$height = 600;
$chart = new Chart($width, $height);
$chart->setRanges(-5, 5, -5, 5);
$chart->setUnit(100, 100);
$chart->setCenter($width/2, $height/2);

$chart->zoom(1,1, 2, 2);

$chart->printTime = true;
$k = 2 * pi();
$w = 2;

$chart->add(function($x, $t) use($k, $w) {
    return 1 *  cos($k * $x * sin($t/4));
}, '#0000FF');
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
    

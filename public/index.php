<?php
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart;

$width = 1000;
$height = 600;
$chart = new Chart($width, $height);
$chart->setRanges(-5, 5, -5, 5);
$k = 2 * pi();
$w = 2;

$chart->plot(function($x, $t) use($k, $w) {
    return 3 *  cos($k * $x * sin($t/4));
}, '#0000FF');
//$file = __DIR__ . '/data/output.png';
//$chart->png($file);

$file = __DIR__ . '/data/output.gif';
$chart->gif($file);
?>
<!DOCTYPE html>
<html>
<body>
<style>
    img {
        
    }
    </style>
<img src="/data/output.gif" />

</body>
</html>
    

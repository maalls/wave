<?php
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart;

$width = 600;
$height = 300;
$chart = new Chart($width, $height);


$chart->plot(function($x, $t) {
    return 3 * cos($t) * cos(2 * $x + pi());
}, '#00FF00');
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
        width: 100%;    
    }
    </style>
<img src="/data/output.gif" />

</body>
</html>
    

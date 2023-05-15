<?php
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart;
use \Maalls\Complex;

$width = 300;
$height = 200;
$chart = new Chart($width, $height);
$chart->setRanges(-2,1, -4,1);
//$chart->setCenter(200, $height/2);
$chart->printTime=true;
$random = function($x, $y, $t) {
    return rand(0, $t) > -1 ? '#FF0000' : '#0000FF';
};
$chart->framePerSecond = 2;
$chart->add($random);
$chart->frameCount = 40;
$file = __DIR__ . '/data/random.png';
$chart->draw($file);

echo "count " . count($chart->hexCache) . "<br/>";
?>
<!DOCTYPE html>
<html>
<body>
<style>
    img {
        width: 400px;
    }
    </style>
<img src="/data/random.png" />
<br/>
<?php   $chart->printParameters() . "<br/>" ?>
</body>
</html>
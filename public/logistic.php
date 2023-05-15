<?php
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart;
use \Maalls\Complex;

$width = 500;
$height = 500;
$chart = new Chart($width, $height);
$chart->setRanges(0,4, 0,1);
//$chart->setCenter($width/2, $height/2);
//$chart->setUnit(100, 100);

$chart->framePerSecond = 12;
$chart->frameCount = $chart->framePerSecond * 3;
$chart->timeUnit = 1;
//$chart->printParameters();
$chart->printTime = true;
$logisticMap = function($x, $t) {

    $r = $x;
    //$c = new Complex($x, $y);
    //$v = new Complex(0, 0);
    $x = 0.2;

    $asymptoticValues = [];
    $n = 1000;
    for($i = 0; $i < $n; $i++) {

        $x = $r * $x * (1 - $x);

        if($x == INF) {
            break;
        }

        if($i > $n - 200) {

            if(!in_array($x, $asymptoticValues)) {
                $asymptoticValues[] = $x;
            }

        }
    }

    return $asymptoticValues;
    


};
$file = __DIR__ . '/data/logistic.png';
/*

$chart->framePerSecond = 1;
$chart->timeUnit = 1;
$chart->gif($logisticMap);
*/
$chart->add($logisticMap);
$chart->draw($file);
//$chart->save($file);
?>
<!DOCTYPE html>
<html>
<body>
<style>
    img {
  
    }
    </style>
<img src="/data/logistic.png" />

</body>
</html>
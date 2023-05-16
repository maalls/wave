<?php 

require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart;

$width = $_GET['width'];
$height = $_GET['height'];

$xMin = $_GET['xMin'];
$xMax = $_GET['xMax'];
$yMin = $_GET['yMin'];
$yMax = $_GET['yMax'];

$chart = new Chart($width, $height);
$chart->setRanges($xMin,$xMax, $yMin, $yMax);
//$chart->setCenter(200, $height/2);
$chart->printTime = true;
$mandelbot = function($x, $y) {

    //$c = new Complex($x, $y);
    //$v = new Complex(0, 0);
    $c = [$x, $y];
    $v = [0,0];

    $reals = [];
    $imaginaries = [];
    $cycle = true;
    for($i = 0; $i < 100; $i++) {

        $t = $v[0]*$v[0] - $v[1]*$v[1] + $c[0];
        $v[1] = 2*$v[0]*$v[1] + $c[1];
        $v[0] = $t;
        if($v[0]*$v[0] > 2 || $v[1] * $v[1] > 2) {
            $cycle = false;
            break;
        }

        $index = array_search($v[0], $reals);
        if($index !== false && $imaginaries[$index] == $v[1]) {

            $cycle = count($reals);
            break;

        }
        $reals[] = $v[0];
        $imaginaries[] = $v[1];


    }
    //echo "($x, $y) cycle $cycle" . PHP_EOL;

    if($cycle === false) {
        return '#000000';
    }
    else {
        return '#FFFFFF';
    }



};

$chart->add($mandelbot);
$file = __DIR__ . "/data/mandelbrot-$width-$height.png";

$chart->frameCount = 10;
$chart->framePerSecond = 2;
$chart->still(1);
$chart->zoom(0,35/$chart->yUnit,2, 2);
$chart->draw($file);

header('Content-Type: application/json; charset=utf-8');

$info = [
    'centerX' => $chart->centerX, 
    'centerY' => $chart->centerY, 
    'xUnit' => $chart->xUnit,
    'yUnit' => $chart->yUnit
];

$data = ['status' => 'ok', 'file' => basename($file), 'info' => $info];
echo json_encode($data);
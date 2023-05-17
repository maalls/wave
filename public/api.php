<?php

require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart\Chart;
use \Maalls\Chart\Algorithm\Mandelbrot;
use \Maalls\Chart\Algorithm\Xtmap;

$width = $_GET['width'];
$height = $_GET['height'];

$xMin = $_GET['xMin'];
$xMax = $_GET['xMax'];
$yMin = $_GET['yMin'];
$yMax = $_GET['yMax'];

$chart = new Chart($width, $height);
$chart->setRanges($xMin, $xMax, $yMin, $yMax);
//$chart->setCenter(200, $height/2);
//$chart->printTime = true;

$algo = new Mandelbrot();
//$algo = new Xtmap();
$chart->add($algo);

$fParams = [$width, $height, $xMin, $xMax, $yMin, $yMax,];

if (isset($_GET['zoom'])) {

    list($xMin, $xMax, $yMin, $yMax) = explode(',', $_GET['zoom']);
    $chart->frameCount = 6;
    $chart->framePerSecond = 2;
    $chart->zoom2($xMin, $xMax, $yMin, $yMax, 3);
    $fParams[] = 'z' . implode("_", [$xMin, $xMax, $yMin, $yMax]);
}
$reflect = new ReflectionClass($algo);
$relativeDir = "data/" . $reflect->getShortName() . "-" . sha1(implode('_', $fParams));
$dir = __DIR__ . "/" . $relativeDir;

if(!file_exists($dir)) {
    mkdir($dir);
    $chart->draw($dir);
}



header('Content-Type: application/json; charset=utf-8');

$info = [
    'centerX' => $chart->centerX,
    'centerY' => $chart->centerY,
    'xUnit' => $chart->xUnit,
    'yUnit' => $chart->yUnit,
    "xMin" => $xMin,
    "xMax" => $xMax,
    "yMin" => $yMin,
    "yMax" => $yMax,
    "frames" => $chart->frameCount
];

$data = ['status' => 'ok', 'dir' => $relativeDir, 'info' => $info];
echo json_encode($data);
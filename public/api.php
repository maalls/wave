<?php

require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart\Chart;
use \Maalls\Chart\Algorithm\Mandelbrot;
use \Maalls\Chart\Algorithm\Xtmap;
use \Maalls\Chart\Algorithm\Logistic;
use \Maalls\Chart\Algorithm\Cos;
$width = getP('width', 200);
$height = getP('height', 200);

$xMin = getP('xMin', -2);
$xMax = getP('xMax', 2);
$yMin = getP('yMin', -2);
$yMax = getP('yMax', 2);

$chart = new Chart($width, $height);
$chart->setRanges($xMin, $xMax, $yMin, $yMax);
//$chart->setCenter(200, $height/2);
//$chart->printTime = true;

$class = getP('class', Logistic::class);
$algo = new $class;

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

if(true || !file_exists($dir)) {
    @mkdir($dir);
    //$chart->multiThread = false;
    /*$chart->frameCount = 200;
    $chart->framePerSecond = 4;
    $chart->rotate(2*pi());
    */
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

function getP($name, $default = null) {
    return isset($_GET[$name]) ? $_GET[$name] : $default;
}
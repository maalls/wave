<?php
ini_set('max_execution_time', -1);
require __DIR__ . '/../vendor/autoload.php';
use \Maalls\Chart\Chart;
use \Maalls\Chart\Algorithm\Mandelbrot;
use \Maalls\Chart\Algorithm\Xtmap;
use \Maalls\Chart\Algorithm\Logistic;
use \Maalls\Chart\Algorithm\Cos;
use \Maalls\Chart\Algorithm\Cube;

$width = getP('width', 200);
$height = getP('height', 200);
$xMin = getP('xMin', -2);
$xMax = getP('xMax', 2);
$yMin = getP('yMin', -2);
$yMax = getP('yMax', 2);
$zMin = getP("zMin", -1);
$zMax = getP("zMax", 1);

$xAngle = getP("xAngle", 0);
$yAngle = getP("yAngle", pi()/2);
$zAngle = getP("zAngle", 0);
$frameRate = getP('frameRate', 1);
$duration = getP("duration", 1);

$chart = new Chart($width, $height);
$chart->setRanges($xMin, $xMax, $yMin, $yMax, $zMin, $zMax);

$chart->setAngles([$xAngle, $yAngle, $zAngle]);
//$chart->setCenter(200, $height/2);


$chart->printTime = getP("printTime", 0);

$chart->framePerSecond = $frameRate;
$chart->frameCount = $duration * $frameRate;
$class = getP('class', Logistic::class);
$algo = new $class;

//$algo = new Xtmap();
$chart->add($algo);

$fParams = [$width, $height, $xMin, $xMax, $yMin, $yMax,$zMin, $zMax, $chart->printTime];

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
    'centerX' => $chart->axes[0]->center,
    'centerY' => $chart->axes[1]->center,
    'xUnit' => $chart->axes[0]->pixelPerUnit,
    'yUnit' => $chart->axes[1]->pixelPerUnit,
    "xMin" => $xMin,
    "xMax" => $xMax,
    "yMin" => $yMin,
    "yMax" => $yMax,
    "zMin" => $zMin,
    "zMax" => $zMax,
    "frames" => $chart->frameCount
];

$data = ['status' => 'ok', 'dir' => $relativeDir, 'info' => $info];
echo json_encode($data);

function getP($name, $default = null) {
    return isset($_GET[$name]) ? $_GET[$name] : $default;
}
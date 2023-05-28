<?php
require __DIR__ . '/../vendor/autoload.php';

use Maalls\Chart\Algorithm\Logistic;
use Maalls\Chart\Algorithm\Mandelbrot;
use Maalls\Chart\Algorithm\Cos;
use Maalls\Chart\Algorithm\Spiral;
use Maalls\Chart\Algorithm\Cube;
use Maalls\Chart\Algorithm\Physics;
use Maalls\Chart\Algorithm\WaveSurface;

$config = [

    'algoritms' => [
        [
            'name' => "Logistic",

            'parameters' => [
                'xMin' => 0,
                'xMax' => 4,
                "xAngle" => 0,
                'yMin' => 0,
                'yMax' => 1,
                'yAngle' => pi() / 2,
                'zMin' => -4,
                'zMax' => 4,
                'zAngle' => 0,
                'width' => 300,
                'height' => 300,
                'class' => Logistic::class,
                'frameRate' => 1,
                'duration' => 1,
                'printTime' => 1
            ]
        ],
        [
            'name' => "Mandelbrot",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 1,
                'xAngle' => 0,
                'yMin' => -1,
                'yMax' => 1,
                'yAngle' => pi() / 2,
                'zMin' => -1,
                'zMax' => 1,
                'zAngle' => 0,
                'width' => 600,
                'height' => 300,
                'class' => Mandelbrot::class,
                'frameRate' => 1,
                'duration' => 1,
                'printTime' => 1
            ]
        ],
        [
            'name' => "Cos",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 2,
                'xAngle' => 0,
                'yMin' => -2,
                'yMax' => 2,
                'yAngle' => pi() / 2,
                'zMin' => -2,
                'zMax' => 2,
                'zAngle' => 0,
                'width' => 600,
                'height' => 300,
                'class' => Cos::class,
                'frameRate' => 1,
                'duration' => 1,
                'printTime' => 1
            ]
        ],
        [
            'name' => "Spiral",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 2,
                'xAngle' => 0,
                'yMin' => -2,
                'yMax' => 2,
                'yAngle' => pi() / 4,
                'zMin' => -2,
                'zMax' => 2,
                'zAngle' => pi() / 2,
                'width' => 600,
                'height' => 300,
                'class' => Spiral::class,
                'frameRate' => 1,
                'duration' => 1,
                'printTime' => 1
            ]
        ],
        [
            'name' => "Cube",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 2,
                'xAngle' => 0,
                'yMin' => -2,
                'yMax' => 2,
                'yAngle' => -pi() / 4,
                'zMin' => -2,
                'zMax' => 2,
                'zAngle' => pi() / 2,
                'width' => 600,
                'height' => 600,
                'class' => Cube::class,
                'frameRate' => 1,
                'duration' => 1,
                'printTime' => 1
            ]
        ],
        [
            'name' => "Physics",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 2,
                'xAngle' => 0,
                'yMin' => -2,
                'yMax' => 2,
                'yAngle' => -pi() / 4,
                'zMin' => -2,
                'zMax' => 2,
                'zAngle' => pi() / 2,
                'width' => 600,
                'height' => 600,
                'class' => Physics::class,
                'frameRate' => 10,
                'duration' => 10,
                'printTime' => 1
            ]
        ],
        [
            'name' => "2D Wave",

            'parameters' => [
                'xMin' => -1,
                'xMax' => 1,
                'xAngle' => 0,
                'yMin' => -1,
                'yMax' => 1,
                'yAngle' => -pi() / 4,
                'zMin' => -1,
                'zMax' => 1,
                'zAngle' => pi() / 2,
                'width' => 600,
                'height' => 600,
                'class' => WaveSurface::class,
                'frameRate' => 4,
                'duration' => 5,
                'printTime' => 1
            ]
        ]
    ]
];

header('Content-Type: application/json; charset=utf-8');

echo json_encode($config);
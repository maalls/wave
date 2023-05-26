<?php
require __DIR__ . '/../vendor/autoload.php';

use Maalls\Chart\Algorithm\Logistic;
use Maalls\Chart\Algorithm\Mandelbrot;
use Maalls\Chart\Algorithm\Cos;
use Maalls\Chart\Algorithm\Spiral;
use Maalls\Chart\Algorithm\Cube;
use Maalls\Chart\Algorithm\Physics;
$config = [

    'algoritms' => [
        [
            'name' => "Logistic",

            'parameters' => [
                'xMin' => 0,
                'xMax' => 4,
                'yMin' => 0,
                'yMax' => 1,
                'zMin' => 0,
                'zMax' => 1,
                'width' => 300,
                'height' => 300,
                'class' => Logistic::class,
                'frameRate' => 1,
                'duration' => 1
            ]
        ],
        [
            'name' => "Mandelbrot",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 1,
                'yMin' => -1,
                'yMax' => 1,
                'zMin' => -1,
                'zMax' => 1,
                'width' => 600,
                'height' => 300,
                'class' => Mandelbrot::class,
                'frameRate' => 1,
                'duration' => 1
            ]
        ],
        [
            'name' => "Cos",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 2,
                'yMin' => -2,
                'yMax' => 2,
                'zMin' => -2,
                'zMax' => 2,
                'width' => 600,
                'height' => 300,
                'class' => Cos::class,
                'frameRate' => 1,
                'duration' => 1
            ]
        ],
        [
            'name' => "Spiral",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 2,
                'yMin' => -2,
                'yMax' => 2,
                'zMin' => -2,
                'zMax' => 2,
                'width' => 600,
                'height' => 300,
                'class' => Spiral::class,
                'frameRate' => 1,
                'duration' => 1
            ]
        ],
        [
            'name' => "Cube",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 2,
                'yMin' => -2,
                'yMax' => 2,
                'zMin' => -2,
                'zMax' => 2,
                'width' => 600,
                'height' => 600,
                'class' => Cube::class,
                'frameRate' => 1,
                'duration' => 1
            ]
        ],
        [
            'name' => "Physics",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 2,
                'yMin' => -2,
                'yMax' => 2,
                'zMin' => -2,
                'zMax' => 2,
                'width' => 600,
                'height' => 600,
                'class' => Physics::class,
                'frameRate' => 10,
                'duration' => 10
            ]
        ]
    ]
];

header('Content-Type: application/json; charset=utf-8');

echo json_encode($config);
<?php
require __DIR__ . '/../vendor/autoload.php';

use Maalls\Chart\Algorithm\Logistic;
use Maalls\Chart\Algorithm\Mandelbrot;
use Maalls\Chart\Algorithm\Cos;
$config = [

    'algoritms' => [
        [
            'name' => "Logistic",

            'parameters' => [
                'xMin' => 0,
                'xMax' => 4,
                'yMin' => 0,
                'yMax' => 1,
                'width' => 300,
                'heigth' => 300,
                'class' => Logistic::class
            ]
        ],
        [
            'name' => "Mandelbrot",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 1,
                'yMin' => -1,
                'yMax' => 1,
                'width' => 600,
                'height' => 300,
                'class' => Mandelbrot::class
            ]
        ],
        [
            'name' => "Cos",

            'parameters' => [
                'xMin' => -2,
                'xMax' => 2,
                'yMin' => -2,
                'yMax' => 2,
                'width' => 600,
                'height' => 300,
                'class' => Cos::class
            ]
        ]
    ]
];

header('Content-Type: application/json; charset=utf-8');

echo json_encode($config);
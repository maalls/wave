<?php 
namespace Maalls\Chart;
class Axis {


    public $angle = 0;
    public $color = null;
    public $pixelPerUnit; // pixel per unit
    public $center; // in pixel

    public $unit = 1; // we will assume 1m
    public $min; // in unit;
    public $max; // in unit;

    public $range;

    public function __construct($angle, $color = "#000000") {

        $this->angle = $angle;
        $this->color = $color;

    }

    public function location($x) {
        list($x, $y) = $this->transform($x);
        return [round($this->toPixel($x)), round($this->toPixel($y))];
    }

    public function centerLocation() {

        

    }
    
    // this gives the distance from center in pixel
    public function toPixel($x) {
        return $x * $this->pixelPerUnit + $this->center;
    }


    public function transform($x) {

        return [$this->transformX($x), $this->transformY($x)];

    }

    public function transformX($x) {
        return $x*cos($this->angle);
    }

    public function transformY($x) {
        return -$x*sin($this->angle);
    }

}
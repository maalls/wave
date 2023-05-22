<?php 
namespace Maalls\Chart;
class Axis {


    public $angle = 0;
    public $min = 0;
    public $max = 0;

    public $size = 0;
    public $centerX = 0;
    public $centerY = 0;

    public $color = null;
    public $unit;
    public $center;

    public function __construct($min, $max, $angle, $size, $color = "#000000") {

        if($min > $max) {
            throw new \Exception("$min has to be less than $max");
        }
        $this->min = $min;
        $this->max = $max;
        $this->angle = $angle;
        $this->size = $size;
        $this->color = $color;

        $this->unit = $this->size / ($this->max - $this->min);
        $this->center = round(-$this->min * $this->unit);
        
    }

    public function getUnit() {

        return $this->size / $this->getRange();
    }

    public function getRange() {

        return $this->max - $this->min;
    }



    public function pixel($x) {

        list($x, $y) = $this->transform($x);

        $x = $x * $this->unit;
        $y = $y * $this->unit;

        return [round($x), round($y)];

    }

    public function transform($x) {

        return [$this->transformX($x), $this->transformY($x)];

    }

    public function transformX($x) {
        return $x*cos($this->angle);
    }

    public function transformY($x) {
        return $x*sin($this->angle);
    }

    public function coordinate($p) {

        return $p*($this->max - $this->min)/$this->size + $this->min;

    }

    public function getCentrePixel() {



    }

}
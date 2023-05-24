<?php 
namespace Maalls\Chart;
class Axis {


    public $angle = 0;
    public $color = null;
    public $unit;
    public $center;

    public function __construct($angle, $color = "#000000") {

       
        
        $this->angle = $angle;
        $this->color = $color;

       
        
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

}
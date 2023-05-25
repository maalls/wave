<?php 

namespace Maalls\Chart\Algorithm;
use Maalls\Chart\Chart;
use Maalls\Chart\Chart\Particle;
class Cube implements Drawing {

    private $position;
    private $speed;
    private $lastTime;

    private $particle;

    public function __construct() {
        $this->position = 1;
        $this->speed = 0;
        $this->lastTime = 0;
    
    }
    public function draw(Chart $chart, $t) {

        // assuming gravity is constant
        $a = -0.1;

        $frameCount = $chart->frameCount / $chart->framePerSecond;
        $deltaT = 1/$chart->framePerSecond;
        $this->speed += $a * $deltaT;
        $this->position += $this->speed * $deltaT;


        

        
        $this->drawCube($chart, 0, 0, 0, 1, 2, "#000000");
        $this->drawCube($chart, 0, 0, $this->position, 0.05, 0.05, "#000000");
        $this->drawCube($chart, 0, 0, -1, 0.05, 0.05, "#FF00FF");
    }

    public function drawCube($chart, $x, $y, $z, $width, $height, $color) {

        $halfWidth = $width/2;
        $halfHeight = $height / 2;
        $xb = $x - $halfWidth;
        $xf = $x + $halfWidth;
        
        
        $yb = $y - $halfWidth;
        $yf = $y + $halfWidth;
        $zb = $z - $halfHeight;
        $zf = $z + $halfHeight;
        $chart->drawLine([$xb, $yb, $zb], [$xb, $yb, $zf], $color);
        $chart->drawLine([$xb, $yb, $zb], [$xf, $yb, $zb], $color);
        $chart->drawLine([$xb, $yb, $zf], [$xf, $yb, $zf], $color);
        $chart->drawLine([$xf, $yb, $zf], [$xf, $yb, $zb], $color);
        $chart->drawLine([$xb, $yf, $zb], [$xb, $yf, $zf], $color);
        $chart->drawLine([$xb, $yf, $zb], [$xf, $yf, $zb], $color);
        $chart->drawLine([$xb, $yf, $zf], [$xf, $yf, $zf], $color);
        $chart->drawLine([$xf, $yf, $zf], [$xf, $yf, $zb], $color);
        $chart->drawLine([$xb, $yb, $zb], [$xb, $yf, $zb], $color);
        $chart->drawLine([$xf, $yb, $zb], [$xf, $yf, $zb], $color);
        $chart->drawLine([$xf, $yb, $zf], [$xf, $yf, $zf], $color);
        $chart->drawLine([$xb, $yb, $zf], [$xb, $yf, $zf], $color);

    }

}


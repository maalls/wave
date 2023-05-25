<?php 

namespace Maalls\Chart\Algorithm;
use Maalls\Chart\Chart;
use Maalls\Chart\Physics\World;
use Maalls\Chart\Physics\Particle;
class Physics implements Drawing {

    private $world;
    private $chart;

    public function __construct() {
        $this->world = new World();

        $particle = new Particle([0,0,0.5]);
        $particle->color = '#FF0000';
        $this->world->addParticle($particle);

        $earthMass = 5.97219 * pow(10,24);
        $particle = new Particle([0,0,-0.5]);
        $particle->color = "#00FFFF";
        $this->world->addParticle($particle);

    
    }
    public function draw(Chart $chart, $t) {

        $this->chart = $chart;
        $this->world->timeUnit = 1/$chart->framePerSecond;
        // assuming gravity is constant
        /*$a = -0.1;

        $frameCount = $chart->frameCount / $chart->framePerSecond;
        $deltaT = 1/$chart->framePerSecond;
        $this->speed += $a * $deltaT;
        $this->position += $this->speed * $deltaT;
        */

        $this->world->iterate();
        //$this->drawCube([0, 0, 0], 1, 2, "#000000");
        

        foreach($this->world->getParticles() as $particle) {
            $this->drawParticle($particle);
        }

        $chart->drawLine($this->world->particles[0]->position, $this->world->particles[1]->position, '#0000FF');
    }

    public function drawParticle($particle) {

        $this->drawCube($particle->position, 0.05, 0.05, $particle->color);

    }

    public function drawCube($position, $width, $height, $color) {

        list($x, $y, $z) = $position;
        $chart = $this->chart;
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


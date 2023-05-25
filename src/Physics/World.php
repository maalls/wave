<?php

namespace Maalls\Chart\Physics;
class World {


    
    public $particles;

    public $timeUnit = 1;
    private $distance;

    private $gravity;


    public function __construct() {

        $this->particles = [];
        $this->gravity =  1; //6.674 * pow(10, -11);

    }

    public function getParticles() {
        return $this->particles;
    }

    public function addParticle(Particle $p) {
        $this->particles[] = $p;
    }

    public function iterate() {

        $newParticles = [];
        foreach($this->particles as $particle) {

            $copy =  $particle;
            $this->applyForces($copy);
            $newParticles[] = $copy;

        }

        $this->particles[] = $copy;

    }

    public function applyForces($particle) {

        foreach($this->particles as $other) {

            $this->applyParticleForce($other, $particle);

        }

    }

    public function applyParticleForce($source, $target) {

        $this->distance = $this->distance($source, $target);
        if($this->distance == 0) return;
        $this->applyGravity($source, $target);

    }

    public function applyGravity($source, $target) {

        if($this->distance == 0) return;
        $a = 0.0001 / $this->distance;
        $a = [0,0,$a]; //$source->mass  * $this->gravity / $this->distance;
    
        for($i = 0; $i < 3; $i++) {

            $normal = $source->position[$i] - $target->position[$i];
            if($normal) {
                $target->speed[$i] = $target->speed[$i] + $this->timeUnit * $a[$i] * ($normal) / abs($normal);
                $target->position[$i] = $target->position[$i] + $target->speed[$i] * $this->timeUnit;
            }
            
        }

    }

    public function distance($source, $target) {

        list($x1, $y1, $z1) = $source->position;
        list($x2, $y2, $z2) = $target->position;

        return sqrt(pow($x1 - $x2,2) + pow($y1 - $y2, 2) + pow($z1 - $z2, 2));
        
    }

}
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
        $a = -0.00000000005 * $source->mass / $this->distance;
        //$a = [0,0,$a]; //$source->mass  * $this->gravity / $this->distance;
        $a = $this->project($a, $this->vector($source, $target));
        //$a = $this->multiple($a, $this->vector($source, $target));
        for($i = 0; $i < 3; $i++) {

            $target->speed[$i] = $target->speed[$i] + $this->timeUnit * $a[$i];
            $target->position[$i] = $target->position[$i] + $target->speed[$i] * $this->timeUnit;
        
        }
        //echo $this->length($a);

        $target->acceleration = $a;

    }

    // cos teta = adj / hyp
    // cos teta = ax / a
    // cos teta * a = ax
    
    // cos teta = px / distance
    // px / distance * a = ax
    // ax = a * px/distance
    public function project($a, $vector) {

        $result = [0,0,0];
        if($this->distance != 0) {
            for($i = 0; $i < 3; $i++) {
                $result[$i] = $a * $vector[$i] / $this->distance;
            }
        }
        return $result;
    }

    public function multiple($a, $vector) {
        $result = [];
        foreach($vector as $v) {
            $result[] = $a * $v;
        }

        return $result;
    }
    public function add($a, $b) {
        $result = [];
        foreach($a as $k => $v) {
            $result[] = $a[$k] + $v;
        }

        return $result;
    }

    public function vector($source, $target) {
        $vector = [];
        foreach($target->position as $k => $v) {
            $vector[] = $v - $source->position[$k];
        }

        return $vector;
    }

    public function length($vector) {

        $sum = 0;
        foreach($vector as $v) {
            $sum += pow($v, 2);
        }

        return sqrt($sum);

    }

    public function distance($source, $target) {

        list($x1, $y1, $z1) = $source->position;
        list($x2, $y2, $z2) = $target->position;

        return sqrt(pow($x1 - $x2,2) + pow($y1 - $y2, 2) + pow($z1 - $z2, 2));
        
    }

    public function vectorToString($v, $precision = 2) {
        $r = [];
        foreach($v as $x) {
            $r[] = round($x, $precision);
        }
        return "(" . implode(",", $r) . ")";
    }

}
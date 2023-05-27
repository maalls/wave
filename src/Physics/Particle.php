<?php 

namespace Maalls\Chart\Physics;

class Particle {

    public $charge;
    public $position;

    public $acceleration;
    public $speed;

    public $mass;

    public $color = '#000000';

    public function __construct($position = [0,0,0], $speed = [0,0,0], $charge = 1, $mass = 1) {
        $this->charge = $charge;
        $this->mass = $mass;
        $this->position = $position;
        $this->speed = $speed;
    }

    public function __clone() {
        return new Particle($this->charge, $this->position, $this->position, $this->speed);
    }
    public function getForce(Particle $p) {

        return $this->charge * $p->charge / $this->distance($p);

    }

    public function distance(Particle $p) {

        return sqrt(pow($p->position[0] - $this->position[0], 2) + pow($p->position[1] - $this->position[1],2) + pow($p->position[2] - $this->position[2], 2));

    }

}
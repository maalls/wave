<?php 

namespace Maalls;

class Complex {

    public $real;
    public $imaginary;

    public function __construct($real, $imaginary) {
        $this->real = $real;
        $this->imaginary = $imaginary;
    }
    public function multiple(Complex $complex) {

        $real = $this->real * $complex->real - $this->imaginary * $complex->imaginary;
        $imaginary = $this->real * $complex->imaginary + $this->imaginary * $complex->real;

        $this->real = $real;
        $this->imaginary = $imaginary;

        return $this;

    }
    public function add(Complex $complex) {
        $this->real += $complex->real;
        $this->imaginary += $complex->imaginary;

        return $this;
    }

    public function square() {
        $this->real = $this->real * $this->real - $this->imaginary * $this->imaginary;
        $this->imaginary = 2 * $this->real * $this->imaginary;

        return $this;
    }

    public function magnitude() {
        return sqrt($this->real* $this->real + $this->imaginary * $this->imaginary);
    }
}
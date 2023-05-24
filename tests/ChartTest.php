<?php

use PHPUnit\Framework\TestCase;
use Maalls\Chart\Chart;
use Maalls\Chart\Algorithm\Mandelbrot;

final class ChartTest extends TestCase
{
    public function testAdd(): void
    {
        $chart = new Chart(100, 100);

        $function = function ($x, $t) {
            return $x * $t;
        };


        $chart->add($function);
        $mandelbrot = new Mandelbrot();
        $chart->add($mandelbrot);
        $this->assertTrue(true);

    }

    public function testXToP(): void
    {
        $chart = new Chart(300, 500);
        $chart->setRanges(-2,1,-2,3);

        $this->assertEquals(200, $chart->xToP(0));
        $this->assertEquals(0, $chart->xToP(-2));
        $this->assertEquals(300, $chart->xToP(1));

        
    }

    public function testPtoX() {
        $chart = new Chart(300, 500);
        $chart->setRanges(-2,1,-2,3);

        $this->assertEquals(0, $chart->pToX(200));
        $this->assertEquals(-2, $chart->pToX(0));
        $this->assertEquals(1, $chart->pToX(300));
    }

    public function testYToP(): void
    {
        $chart = new Chart(300, 500);
        $chart->setRanges(-2,1,-2,3);

        $this->assertEquals(300, $chart->yToP(0));
        $this->assertEquals(500, $chart->yToP(-2));
        $this->assertEquals(0, $chart->yToP(3));

        
    }

    public function testPToY(): void
    {
        $chart = new Chart(300, 500);
        $chart->setRanges(-2,1,-2,3);

        $this->assertEquals(0, $chart->pToY(300));
        $this->assertEquals(-2, $chart->pToY(500));
        $this->assertEquals(3, $chart->pToY(0));

        
    }

    public function testTransform(): void 
    {
        $chart = new Chart(400, 600);
        $chart->setRanges(-2,2, -3,3);

        
        $this->assertEquals([0, 0], $chart->transform(-2,3));
        $this->assertEquals([0, 600], $chart->transform(-2,-3));
        $this->assertEquals([400, 600], $chart->transform(2,-3));
        $this->assertEquals([400, 0], $chart->transform(2,3));
        $this->assertEquals([200, 300], $chart->transform(0,0));

    }

    

}
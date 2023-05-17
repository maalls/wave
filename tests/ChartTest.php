<?php 

use PHPUnit\Framework\TestCase;
use Maalls\Chart\Chart;
use Maalls\Chart\Algorithm\Mandelbrot;
final class ChartTest extends TestCase
{
    public function testAdd(): void
    {
        $chart = new Chart(100,100);

        $function = function($x, $t) {
            return $x * $t;
        };

        
        $chart->add($function);
        $mandelbrot = new Mandelbrot();
        $chart->add($mandelbrot);
        
    }

}
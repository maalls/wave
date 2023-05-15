<?php 

use PHPUnit\Framework\TestCase;

final class ChartTest extends TestCase
{
    public function testAdd(): void
    {
        $chart = new \Maalls\Chart(100,100);

        $chart->add(function($x, $t) {
            return $x * $t;
        });
        
    }

}
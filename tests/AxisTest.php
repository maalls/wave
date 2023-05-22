<?php

use PHPUnit\Framework\TestCase;
use Maalls\Chart\Axis;


final class AxisTest extends TestCase
{

    public function testToPixel(): void
    {
        $chart = new Axis(-2, 1, 0, 300);
        
        $this->assertEquals(200, $chart->toPixel(0));
        $this->assertEquals(0, $chart->toPixel(-2));
        $this->assertEquals(300, $chart->toPixel(1));

        
    }

    public function testToCoordinate(): void
    {
        $chart = new Axis(-2, 1, 0, 300);
        
        $this->assertEquals(0, $chart->toCoordinate(200));
        $this->assertEquals(-2, $chart->toCoordinate(0));
        $this->assertEquals(1, $chart->toCoordinate(300));

    }

    

}
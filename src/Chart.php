<?php

namespace Maalls;
use GifCreator\AnimGif;
class Chart {

    public $width;
    public $height;
    public $image;

    public $x0;
    public $y0;
    public $xMin;
    public $xMax;
    public $yMin;
    public $yMax;
    public $xRange;
    public $yRange;
    public $xRatio;
    public $yRatio;

    public $xStepCount;
    public $yStepCount;
    public $color;

    public $axisColor;

    public $pixelPerUnit = 20;

    public $unit = 40;

    public $timeUnit = 16; 
    public $framePerSecond = 24;
    public $stepCount;

    public $functions = [];

    public $hexCache = [];

    public function __construct($width, $height) {
        
        $this->width = $width;
        $this->height = $height;
        $this->x0 = round($this->width/2);
        $this->y0 = round($height / 2);
        $this->setRanges(-$width/2 / 10, $width/2 / 10, -$height/2 / 10, $height/2 / 10);
        $this->xStepCount = floor($width/2 / $this->unit);
        $this->yStepCount = floor($height / 2 / $this->unit);

        
        

    }

    public function pixel($x, $y) {

        imagesetpixel($this->image, $this->x($x), $this->y($y), $this->color);

    }

    public function x($x) {
        return round($x);
    }

    public function y($y) {
        
        return round(($this->height/2 - $y*$this->unit));
    }

    public function plot($callback, $hex = null) {

        $this->functions[] = [$callback, $hex];
        
        
    }

    public function gif($file) {

        
        $dir = sys_get_temp_dir();
        $frames = [];
        $durations = [];
        $duration = round(100 / $this->framePerSecond);
        for($t = 0; $t < 400; $t++) {

            $frame = $dir . '/' . $t . '.png';
            $this->draw($t / $this->timeUnit);
            imagepng($this->image, $frame);
            $frames[] = $frame;
            $durations[] = $duration;

        }

        $anim = new AnimGif();
        $anim->create($frames, $durations);
        $anim->save($file);
        
    }

    public function setHexColor($hex) {

        if(isset($this->hexCache[$hex])) {
            $this->color = $this->hexCache[$hex];
        }
        else {
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $this->color = imagecolorallocate($this->image, $r, $g, $b);
            $this->hexCache[$hex] = $this->color;
        }

        
    }

    public function cos() {
        return $this->plot(function($a) {
            return 2 * cos($a);
        });
    }

    public function setRanges($xMin, $xMax, $yMin, $yMax) {
        $this->xMin = round($xMin);
        $this->xMax = round($xMax);
        $this->yMin = round($yMin);
        $this->yMax = round($yMax);
        $this->xRange = $this->xMax - $this->xMin;
        $this->yRange = $this->yMax - $this->yMin;
        $this->xRatio = $this->xRange / $this->width; 
        $this->yRatio = $this->yRange / $this->height;

    }

    public function png($file = null) {

        $this->draw(0);
        return imagepng($this->image, $file);
    }

    public function paint($function) {

       

        
        // ex: width is 100, range is 10
        // when i is 0, x should be -5
        // when i is 100, x should be 5;
        

        $this->new();
      
        for($i = 0; $i < $this->width; $i++) {
            echo "$i" . PHP_EOL;
            for($j = 0; $j < $this->height; $j++) {

                $x = $this->xMin + $i * $this->xRatio;
                $y = $this->yMax - $j * $this->yRatio;
                //$x = $i - $this->x0;
                //$y = $this->y0 - $j;
                $hex = $function($x, $y);
                $this->setHexColor($hex);
                imagesetpixel($this->image, $i, $j, $this->color);
                
            
            }
            
        }


    }

    public function save($file) {
        return imagepng($this->image, $file);
    }

    public function new() {
        $this->image = imagecreate($this->width, $this->height);
        imagecolorallocate($this->image, 255, 255, 0);
        $this->axisColor = imagecolorallocate($this->image, 0, 0, 255);
        $this->color = imagecolorallocate($this->image, 0, 0, 0);
    }


    public function draw($t = 0) {

        $this->new();
        $this->drawAxes();


        foreach($this->functions as $function) {

            if($function[1]) {
                $color = $this->color;
                $this->setHexColor($function[1]);
            }
    
            for($i = 0; $i < $this->width; $i++) {
    
                $x = $this->xMin - $i * $this->xRatio;

                $this->pixel($i, $function[0]($x, $t));
    
            }
            if($function[1]) {
                $this->color = $color;
            }

        }

    }

    public function drawAxes() {

        $centerX = round($this->width/2);
        $centerY = round($this->height / 2);
        imageline($this->image, 0, $centerY, $this->width, $centerY, $this->axisColor);
        imageline($this->image, $centerX, 0, $centerX, $this->height, $this->axisColor);

        // length in pixel of a unit
        $xUnitLength = floor($this->width / $this->xRange);
        $yUnitLength = floor($this->height / $this->yRange);
        // number of unit
        
        $tickSize = 5;
        for($i = $centerX + $this->xMin * $xUnitLength; $i <= $this->width; $i += $xUnitLength) {
            imageline($this->image, $i, $centerY - $tickSize, $i, $centerY +$tickSize, $this->axisColor);
        }

        for($i = $centerY + $this->yMin * $yUnitLength; $i <= $this->height; $i += $yUnitLength) {
            imageline($this->image, $centerX - $tickSize, $i, $centerX + $tickSize, $i, $this->axisColor);
        }
    }


}
<?php

namespace Maalls;
use GifCreator\AnimGif;
class Chart {

    public $width;
    public $height;
    public $image;

    public $x0;
    public $y0;
    public $xMax;
    public $yMax;
    public $color;

    public $axisColor;

    public $pixelPerUnit = 20;

    public $unit = 40;

    public $timeUnit = 4; 
    public $stepCount;

    public $functions = [];

    public function __construct($width, $height) {
        
        $this->width = $width;
        $this->height = $height;
        $this->x0 = round($this->width/2);
        $this->y0 = round($height / 2);
        $this->xMax = $width;
        $this->yMax = 0;
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
        for($t = 0; $t < 100; $t++) {

            $frame = $dir . '/' . $t . '.png';
            $this->draw($t / $this->timeUnit);
            imagepng($this->image, $frame);
            $frames[] = $frame;
            $durations[] = 4;

        }

        $anim = new AnimGif();
        $anim->create($frames, $durations);
        $anim->save($file);
        
    }

    public function setHexColor($hex) {
        list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
        $this->color = imagecolorallocate($this->image, $r, $g, $b);
    }

    public function cos() {
        return $this->plot(function($a) {
            return 2 * cos($a);
        });
    }

    public function png($file = null) {

        $this->draw(0);
        return imagepng($this->image, $file);
    }


    public function draw($t = 0) {

        $this->image = imagecreate($this->width, $this->height);
        imagecolorallocate($this->image, 255, 255, 255);
        $this->axisColor = imagecolorallocate($this->image, 0, 0, 255);
        $this->color = imagecolorallocate($this->image, 0, 0, 0);

        imageline($this->image, 0, $this->y0, $this->xMax, $this->y0, $this->axisColor);
        imageline($this->image, $this->x0, 0, $this->x0, $this->height, $this->axisColor);

        for($i = $this->x0 - ($this->xStepCount * $this->unit); $i < $this->width; $i += $this->unit) {
            imageline($this->image, $i, $this->y0 - 10, $i, $this->y0 +10, $this->axisColor);
        }

        for($i = $this->y0 - ($this->yStepCount * $this->unit); $i < $this->height; $i += $this->unit) {
            imageline($this->image, $this->x0 - 10, $i, $this->x0 + 10, $i, $this->axisColor);
        }


        foreach($this->functions as $function) {

            if($function[1]) {
                $color = $this->color;
                $this->setHexColor($function[1]);
            }
    
            for($i = 0; $i < $this->width; $i++) {
    
                $this->pixel($i, $function[0](($i - $this->x0) / $this->unit, $t));
    
            }
            if($function[1]) {
                $this->color = $color;
            }

        }

    }


}
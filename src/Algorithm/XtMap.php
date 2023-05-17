<?php 

namespace Maalls\Chart\Algorithm;

class Xtmap {

    public  $gama = 0.80;
    public  $intensityMax = 255;

    public $minimumLength = 380;
    public $maximumLength = 781;

    public $max = 10;

    public function init($chart) {

        $this->max = $chart->xMax + $chart->yMax;
        //echo $this->max . PHP_EOL;

    }
    
    public function map($x, $y) {

        $sum = $x + $y;

        $length = 380 + ($sum)/$this->max * ($this->maximumLength - $this->minimumLength);
        $rgb = $this->waveLengthToRGB($length);
        //echo "$x, $y : $sum : $length (" . implode(',', $rgb) . ")" . PHP_EOL;
        return $this->rgbToHexa($rgb);

    }

    public function waveLengthToRGB($wavelength) {
        
        if(($wavelength >= 380) && ($wavelength < 440)) {
            $red = -($wavelength - 440) / (440 - 380);
            $green = 0.0;
            $blue = 1.0;
        } else if(($wavelength >= 440) && ($wavelength < 490)) {
            $red = 0.0;
            $green = ($wavelength - 440) / (490 - 440);
            $blue = 1.0;
        } else if(($wavelength >= 490) && ($wavelength < 510)) {
            $red = 0.0;
            $green = 1.0;
            $blue = -($wavelength - 510) / (510 - 490);
        } else if(($wavelength >= 510) && ($wavelength < 580)) {
            $red = ($wavelength - 510) / (580 - 510);
            $green = 1.0;
            $blue = 0.0;
        } else if(($wavelength >= 580) && ($wavelength < 645)) {
            $red = 1.0;
            $green = -($wavelength - 645) / (645 - 580);
            $blue = 0.0;
        } else if(($wavelength >= 645) && ($wavelength < 781)) {
            $red = 1.0;
            $green = 0.0;
            $blue = 0.0;
        } else {
            $red = 0.0;
            $green = 0.0;
            $blue = 0.0;
        }
    
        // Let the intensity fall off near the vision limits
        if($wavelength < 380) {
            return [0,255,0];
        }
        else if(($wavelength >= 380) && ($wavelength < 420)) {
            $factor = 0.3 + 0.7 * ($wavelength - 380) / (420 - 380);
        }
        else if(($wavelength >= 420) && ($wavelength < 701)) {
            $factor = 1.0;
        }
        else if(($wavelength >= 701) && ($wavelength < 781)) {
            $factor = 0.3 + 0.7 * (780 - $wavelength) / (780 - 700);
        } else {
            return [255,255,255];
        }
    
    
        $rgb = [];
    
        // Don't want 0^x = 1 for x <> 0
        $rgb[0] = $red == 0.0 ? 0 : (int)round($this->intensityMax * pow($red * $factor, $this->gama));
        $rgb[1] = $green == 0.0 ? 0 : (int)round($this->intensityMax * pow($green * $factor, $this->gama));
        $rgb[2] = $blue == 0.0 ? 0 : (int)round($this->intensityMax * pow($blue * $factor, $this->gama));
    
        return $rgb;
    }

    public function rgbToHexa($rgb) {
        return $color = sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
    }


}
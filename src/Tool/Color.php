<?php 

namespace Maalls\Chart\Tool;

class Color {

    public static function waveLengthToRGB($wavelength, $intensityMax = 0.8, $gama = 255) {
        
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
        $rgb[0] = $red == 0.0 ? 0 : (int)round($intensityMax * pow($red * $factor, $gama));
        $rgb[1] = $green == 0.0 ? 0 : (int)round($intensityMax * pow($green * $factor, $gama));
        $rgb[2] = $blue == 0.0 ? 0 : (int)round($intensityMax * pow($blue * $factor, $gama));
    
        return $rgb;
    }

    public static function rgbToHexa($rgb) {
        return $color = sprintf("#%02x%02x%02x", $rgb[0], $rgb[1], $rgb[2]);
    }

}
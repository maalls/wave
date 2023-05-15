<?php

namespace Maalls;
use GifCreator\AnimGif;
class Chart {

    public $width;
    public $height;
    public $image;


    public $xUnit;
    public $yUnit;
    public $xMin;
    public $xMax;
    public $yMin;
    public $yMax;
    public $xRange;
    public $yRange;

    public $centerX;
    public $centerY;

    public $color;

    public $black;

    public $background;
    public $axisColor;

    public $unit = 40;

    public $timeUnit = 16; 

    public $frameCount = 400;
    public $framePerSecond = 24;
    public $functions = [];

    public $hexCache = [];

    public $useTime = false;

    public $showAxes = true;

    public $printTime = false;

    public $motions = [];

    public function __construct($width, $height) {
        
        $this->width = $width;
        $this->height = $height;
        //$this->setCenter(round($this->width/2), round($height / 2));
        $this->setRanges(-$width/2 / 10, $width/2 / 10, -$height/2 / 10, $height/2 / 10);
        
        $this->image = imagecreate($this->width, $this->height);
        
        $this->black = imagecolorallocate($this->image, 0,0,0);
        $this->color = $this->black;
        $this->background = imagecolorallocate($this->image, 255, 255, 255);
        $this->axisColor = imagecolorallocate($this->image, 0, 0, 255);
        $this->init();

    }

    public function pixel($x, $y) {

        imagesetpixel($this->image, $this->x($x), $this->y($y), $this->color);

    }

    public function x($x) {
        return round($x);
    }

    public function y($y) {
        
        return (int)number_format(($this->centerY - $y * $this->yUnit), 0, '','');
    }

    public function add($function, $hexColor = null) {
        $f = new \ReflectionFunction($function);
        $names = [];
        foreach($f->getParameters() as $parameter) {

            $names[] = $parameter->name;

        }

        switch($names) {
            case ['x', 't'] :
                $type = 'xt';
                $this->useTime = true;
                break;
            case ['x']:
                $type = 'x';
                break;
            case ['x', 'y']:
                $type = 'xy';
                break;
            case ['x', 'y', 't']:
                $type = 'xyt';
                $this->useTime = true;
                break;
        }

        $this->functions[] = [$function, $hexColor, $type];
    }

    public function plot($callback, $hex = null) {

        $this->add($callback, $hex);
        
        
    }

    public function draw($file) {

        
        $dir = sys_get_temp_dir();
        $frames = [];
        $durations = [];
        $duration = round(100 / $this->framePerSecond);

        if(!$this->useTime) {
            $frameCount = 1;
        }
        else {
            $frameCount = $this->frameCount;
        }

        
        $motion = null;
        $lastFrame =  null;
        $xMinStep = $xMaxStep = $yMinStep = $yMaxStep = 0;

        for($t = 0; $t < $frameCount; $t++) {
            echo "t$t";
            $frame = $dir . '/' . $t . '.png';

            $this->init();

            if($motion && $t > $lastFrame) {
                echo "REMOVE";          
               $motion = null;
                
            }
            if($this->motions && !$motion) {
               
                $motion = array_shift($this->motions);
                echo "[" . $motion[0] . "]";
                switch($motion[0]) {
                    case 'still':
                        $lastFrame = $t + $this->framePerSecond * $motion[1] - 1;
                        break;
                    case "pan":
                        $distanceX = $motion[1];
                        $distanceY = $motion[2];
                        $framePerDuration = $this->framePerSecond * $motion[3];
                        $xMinStep = $xMaxStep = $distanceX / $framePerDuration;
                        $yMinStep = $yMaxStep = $distanceY / $framePerDuration;
                        $lastFrame = $t + $framePerDuration - 1;
                        break;
                    case "zoom":
                        
                        $targetRangeX = $this->xRange / $motion[3] - $this->xRange;
                        $targetRangeY = $this->yRange / $motion[3] - $this->yRange;
                        echo $this->xRange . ';'.$targetRangeX;
                        $framePerDuration = $this->framePerSecond * $motion[4];
                        $xMaxStep = $targetRangeX / $framePerDuration / 2;
                        $yMaxStep = $targetRangeY / $framePerDuration / 2;
                        $xMinStep = -$xMaxStep;
                        $yMinStep = -$yMaxStep;
                        $lastFrame = $t + $framePerDuration - 1;
                        echo 'm' . $xMaxStep;
                        
                        
                   
                        break;

                }
                
                
            }

            if($motion && $motion[0] != 'still') {
                $this->setRanges($this->xMin + $xMinStep, $this->xMax + $xMaxStep, $this->yMin + $yMinStep, $this->yMax + $yMaxStep);

            }
            
            $scaledT = $t / $this->framePerSecond;
            foreach($this->functions as $function) {

                switch($function[2]) {

                    case 'x': // constant over time
                    case 'xt':
                        $this->drawX($function[0], $function[1], $scaledT);
                        
                        break;
                    case 'xy':
                    case 'xyt':
                        $this->drawXY($function[0], $scaledT);
                }

            }
            if($this->showAxes) {
                $this->drawAxes();
            }
            
            if($this->printTime) {
                $time = round($t / $this->framePerSecond, 2);
                $fontPath = __DIR__ . '/font/Helvetica.ttf';
                $text = str_pad(number_format($time, 1), 3, " ", STR_PAD_LEFT) ." sec " . str_pad($t + 1, strlen($frameCount), ' ', STR_PAD_LEFT) . "/" . $frameCount;

                if(true) {

                    $text .= " d($this->width, $this->height)  c($this->centerX, $this->centerY) mm([" . round($this->xMin,2) . "," . round($this->xMax) . ',('. round($this->yMin) . ',' . round($this->yMax). ")] u(".round($this->xUnit,1).','.round($this->yUnit,1).") r(".round($this->xRange) .','.round($this->yRange).")";

                }

                $bbox = imagettfbbox(8, 0, $fontPath, $text);
                
                $textHeight = $bbox[0] - $bbox[7] + 2;
                $textWidth = $bbox[2] - $bbox[0] + 2;
                imagefilledrectangle($this->image, 0, $this->height - $textHeight - 2,$textWidth, $this->height, $this->background);
                imagettftext($this->image, 8, 0, 0, $this->height - 2, $this->black, $fontPath, $text);
            }
            imagefilledrectangle($this->image, $this->width /2 - 1, $this->height/2 - 1, $this->width/2+1, $this->height/2+1, $this->black);
            imagepng($this->image, $frame);
            $frames[] = $frame;
            $durations[] = $duration;

        }
        
              
        if($this->useTime) {

            $anim = new AnimGif();
            $anim->create($frames, $durations);
            $anim->save($file);
        }
        else {

            imagepng($this->image, $file);

        }
        
    }

    public function drawXY($function, $t) {
      
        for($i = 0; $i < $this->width; $i++) {
      
            for($j = 0; $j < $this->height; $j++) {

      
                $x = ($i - $this->centerX) / $this->xUnit;
                $y = ($j - $this->centerY) / $this->yUnit;
                $hex = $function($x, $y, $t);
                $this->setHexColor($hex);
                imagesetpixel($this->image, $i, $j, $this->color);
                
            
            }
            
        }


    }

    public function drawX($function, $hex = null, $t = 0) {

        
        if($hex) {
            $color = $this->color;
            $this->setHexColor($hex);
        }

        for($i = 0; $i < $this->width; $i++) {

            $x = $this->xMin + $i / $this->xUnit;

            $result = $function($x, $t);
            if(!is_array($result)) {
                $result = [$result];
            }   
            foreach($result as $y) {
                $this->pixel($i, $y);
            } 
            
            

        }
        if($hex) {
            $this->color = $color;
        }
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

    public function setCenter($centerX, $centerY) {
        $this->centerX = round($centerX);
        $this->centerY = round($centerY);
    }

    public function setRanges($xMin, $xMax, $yMin, $yMax) {
        $this->xMin = $xMin;
        $this->xMax = $xMax;
        $this->yMin = $yMin;
        $this->yMax = $yMax;
        $this->xRange = $this->xMax - $this->xMin;
        $this->yRange = $this->yMax - $this->yMin;
        $this->xUnit = $this->width / $this->xRange;
        $this->yUnit = $this->height / $this->yRange;

      
        $this->centerX = round(-$this->xMin * $this->xUnit);
        $this->centerY = $this->height + round($this->yMin * $this->yUnit);
        
        
    }

    public function setUnit($xUnit, $yUnit) {
        $this->xUnit = $xUnit;
        $this->yUnit = $yUnit;

        $this->xRange = $this->width / $this->xUnit;
        $this->yRange = $this->height / $this->yUnit;

       $this->xMin = -floor($this->centerX / $this->xUnit);
       $this->xMax = floor(($this->width - $this->centerX) / $this->xUnit);
       $this->yMin = -floor($this->centerY / $this->yUnit);
       $this->yMax = ($this->height - $this->centerY) / $this->yUnit;
        //echo "unit: ($this->xUnit, $this->yUnit), range: ($this->xRange, $this->yRange)" . PHP_EOL;
        //echo "minmax: ($this->xMin,$this->xMax), ($this->yMin,$this->yMax)" . PHP_EOL;
    }

    public function png($file = null) {

        $this->draw(0);
        return imagepng($this->image, $file);
    }

    

    public function save($file) {
        return imagepng($this->image, $file);
    }

    public function init() {
        //echo "ff";
        //$this->image = imagecreate($this->width, $this->height);
        imagefill($this->image, 0,0, $this->background);
        //imagecolorallocate($this->image, 255, 255, 0);
        imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $this->background);
                
    }


    

    public function drawAxes() {

        imageline($this->image, 0, $this->centerY, $this->width, $this->centerY, $this->axisColor);
        imageline($this->image, $this->centerX, 0, $this->centerX, $this->height, $this->axisColor);

        
        $tickSize = 5;
        
        for($i = $this->centerX - $this->xUnit * floor($this->centerX/$this->xUnit); $i <= $this->width; $i += $this->xUnit) {
            imageline($this->image, round($i), $this->centerY - $tickSize, round($i), $this->centerY +$tickSize, $this->axisColor);
        }
        
    
        for($i = $this->centerY + $this->yUnit * floor($this->centerY/$this->yUnit); $i >= 0; $i -= $this->yUnit) {
            imageline($this->image, round($this->centerX - $tickSize), round($i), round($this->centerX + $tickSize), round($i), $this->axisColor);
        }
    }

    public function printParameters() {
        echo "dimension: ($this->width, $this->height)" . PHP_EOL;
        echo "center: ($this->centerX, $this->centerY), unit: ($this->xUnit, $this->yUnit)" . PHP_EOL;
        echo "minmax: [($this->xMin, $this->xMax), ($this->yMin, $this->yMax)]" . PHP_EOL;
        echo "range: ($this->xRange, $this->yRange)" . PHP_EOL;
    }


    public function pan($targetX, $targetY, $duration) {


        $this->motions[] = ['pan', $targetX, $targetY, $duration];
    }

    public function zoom($targetX, $targetY, $level, $duration) {


        $this->motions[] = ['zoom', $targetX, $targetY,  $level, $duration];
    }

    public function still($duration) {
        $this->motions[] = ['still', $duration];
    }


}
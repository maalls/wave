<?php

namespace Maalls\Chart;

use GifCreator\AnimGif;
use Maalls\Chart\Algorithm\Drawing;
use Maalls\Chart\Algorithm\Surjectif;
use Maalls\Chart\Tool\MultiThread;
use Maalls\Chart\Axis;
use Maalls\Chart\Algorithm\Func;

class Chart
{

    public $width;
    public $height;
    public $image;


    public $axes;

    public $color;

    public $black;

    public $background;
    public $axisColor;


    public $frameCount = 100;
    public $framePerSecond = 10;
    public $functions = [];

    public $hexCache = [];

    public $useTime = false;

    public $showAxes = true;

    public $printTime = false;

    public $motions = [];

    protected $transforms = [];

    public $logger;

    public $multiThread = false;

    public $dir;

    public $startRanges;

    public function __construct($width, $height)
    {

        $this->width = $width;
        $this->height = $height;
        $this->axes = [
            new Axis(0,  "#00FF00"),
            new Axis(-pi()/ 4, "#FF00FF"),
            new Axis(pi()/2, "#00FFFF"),
        ];
        //$this->setCenter(round($this->width/2), round($height / 2));
        $this->setRanges(-$width / 2, $width / 2, -$height / 2, $height / 2, -$height / 2, $height / 2);

        $this->image = imagecreatetruecolor($this->width, $this->height);
        imageantialias($this->image, true);

        $this->black = imagecolorallocate($this->image, 0, 0, 0);
        $this->color = $this->black;
        $this->background = imagecolorallocate($this->image, 255, 255, 255);
        $this->axisColor = imagecolorallocate($this->image, 0, 0, 255);

        
        $this->init();





    }

    public function add($function, $hexColor = null)
    {

        $names = [];

        if (is_a($function, \Closure::class)) {
            $f = new \ReflectionFunction($function);
        } 
        else if(is_a($function, Drawing::class)) {
            $f = new \ReflectionMethod(get_class($function), "draw");
            $this->useTime = true;
        }
        else {

            $f = new \ReflectionMethod(get_class($function), "map");

        }

        foreach ($f->getParameters() as $parameter) {

            $names[] = $parameter->name;

        }


        switch ($names) {
            case ['x', 't']:
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
            default:
                $type = null;
        }

        $this->functions[] = [$function, $hexColor, $type];
    }

    public function plot($callback, $hex = null)
    {

        $this->add($callback, $hex);


    }

    public function draw($file)
    {

        if (is_dir($file)) {
            $this->dir = $file;
            if (!file_exists($this->dir)) {
                mkdir($this->dir);
            }
        } else {
            $this->dir = sys_get_temp_dir();
        }

        $frames = [];
        $durations = [];
        $duration = round(100 / $this->framePerSecond);

        
        
        $motion = null;
        $lastFrame = null;
        $xMinStep = $xMaxStep = $yMinStep = $yMaxStep = $angleStep = 0;

        $this->transforms = [];
        if ($this->motions) {

            for ($t = 0; $t < $this->frameCount; $t++) {
                if ($motion && $t > $lastFrame) {
                    //echo "REMOVE";          
                    $motion = null;

                }
                if ($this->motions && !$motion) {

                    $motion = array_shift($this->motions);
                    //echo "[" . $motion[0] . "]";
                    switch ($motion[0]) {
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
                        case 'zoom2':

                            $framePerDuration = $this->framePerSecond * $motion[5];
                            $xMinStep = ($motion[1] - $this->axes[0]->min) / $framePerDuration;
                            $xMaxStep = ($motion[2] - $this->axes[0]->max) / $framePerDuration;
                            $yMinStep = ($motion[3] - $this->axes[1]->min) / $framePerDuration;
                            $yMaxStep = ($motion[4] - $this->axes[1]->max) / $framePerDuration;
                            $lastFrame = $t + $framePerDuration - 1;

                            break;



                        case "zoom":

                            $framePerDuration = $this->framePerSecond * $motion[4];
                            $distanceX = $motion[1] * ($motion[3] >= 1 ? $motion[3] : 1 / $motion[3]);
                            $distanceY = $motion[2] * ($motion[3] >= 1 ? $motion[3] : 1 / $motion[3]);
                            $xMinStep = $xMaxStep = $distanceX / $framePerDuration;
                            $yMinStep = $yMaxStep = $distanceY / $framePerDuration;

                            $targetRangeX = $this->axes[0]->range / $motion[3] - $this->axes[0]->range - $distanceX;
                            $targetRangeY = $this->axes[1]->range / $motion[3] - $this->axes[1]->range - $distanceY;

                            $xMaxStep += $targetRangeX / $framePerDuration / 2;
                            $yMaxStep += $targetRangeY / $framePerDuration / 2;
                            $xMinStep += -$xMaxStep;
                            $yMinStep += -$yMaxStep;
                            $lastFrame = $t + $framePerDuration - 1;
                            break;

                        case "rotate":
                            $duration = $motion[2] ? $motion[2] : $this->frameCount / $this->framePerSecond;
                            $framePerDuration = $this->framePerSecond * $duration;
                            $angle = $motion[1];
                            $angleStep = $angle / $framePerDuration;
                            $lastFrame = $t + $framePerDuration - 1;
                            break;

                    }
                }

                if ($motion && $motion[0] != 'still') {
                    if ($t > 0) {
                        $prev = [$this->transforms[$t - 1][0], $this->transforms[$t - 1][1], $this->transforms[$t - 1][2], $this->transforms[$t - 1][3], $this->transforms[$t - 1][4]];
                    } else {
                        $prev = [0, 0, 0, 0, 0];
                    }
                    $this->transforms[$t] = [$xMinStep + $prev[0], $xMaxStep + $prev[1], $yMinStep + $prev[2], $yMaxStep + $prev[3], $prev[4] + $angleStep];
                } else {
                    $this->transforms[$t] = [0, 0, 0, 0, 0];
                }


            }


        }
        $this->startRanges = [$this->axes[0]->min, $this->axes[0]->max, $this->axes[1]->min, $this->axes[1]->max];

        if ($this->multiThread) {
            $multiThread = new MultiThread();
            $multiThread->iterate([$this, "drawT"], 0, $this->frameCount);

        } else {
            for ($t = 0; $t < $this->frameCount; $t++) {

                $frame = $this->drawT($t);

            }
        }


        if ($this->frameCount > 1) {

            if (!is_dir($file)) {
                // todo
                //$anim = new AnimGif();
                //$anim->create($frames, $durations);
                //$anim->save($file);
            }

        }

    }

    public function drawT($t)
    {

        $frame = $this->dir . '/' . ($t + 1) . '.png';

        $this->init();

        if ($this->showAxes) {
            $this->drawAxes();
        }

        if ($this->transforms && $this->transforms[$t]) {
            list($xMinStep, $xMaxStep, $yMinStep, $yMaxStep, $angleStep) = $this->transforms[$t];
            $this->setRanges($this->startRanges[0] + $xMinStep, $this->startRanges[1] + $xMaxStep, $this->startRanges[2] + $yMinStep, $this->startRanges[3] + $yMaxStep, $this->startRanges[2] + $yMinStep, $this->startRanges[3] + $yMaxStep);
            
        }

        $scaledT = $t / $this->framePerSecond;
        foreach ($this->functions as $function) {

            if(is_a($function[0], Drawing::class)) {
                $function[0]->draw($this, $scaledT);
            }
            else {

                if (method_exists($function[0], 'init')) {
                    $function[0]->init($this);
                }
                switch ($function[2]) {

                    case 'x': // constant over time
                    case 'xt':
                        $this->drawX($function[0], $function[1], $scaledT);
                        break;
                    case 'xy':
                    case 'xyt':
                        $this->drawXY($function[0], $scaledT);
                }
            }

        }
        

        if ($this->printTime) {
            $time = round($t / $this->framePerSecond, 2);
            
            $text = str_pad(number_format($time, 1), 3, " ", STR_PAD_LEFT) . " sec " . str_pad($t + 1, strlen($this->frameCount), ' ', STR_PAD_LEFT) . "/" . $this->frameCount;
            $text .= " d($this->width, $this->height)  c(".$this->axes[0]->center.", ".$this->axes[1]->center.") mm[(" . round($this->axes[0]->min, 1) . "," . round($this->axes[0]->max, 1) . '),(' . round($this->axes[1]->min, 1) . ',' . round($this->axes[1]->max, 1) . ")] u(" . round($this->axes[0]->pixelPerUnit, 1) . ',' . round($this->axes[1]->pixelPerUnit, 1) . ") r(" . round($this->axes[0]->range) . ',' . round($this->axes[1]->range) . ")";
            $this->drawText($text, $this->height - 20, 0);
            $units = [];
            $centers = [];
            foreach($this->axes as $axe) {
                $units[] = $axe->pixelPerUnit;
                $centers[] = $axe->center;
            }
            $this->drawText("unit:(" . implode(',',$units) . ') center: (' . implode(",", $centers) . ')', $this->height - 10);
        }
        imagefilledrectangle($this->image, $this->width /2 - 1, $this->height/2 - 1, $this->width/2+1, $this->height/2+1, $this->black);
        $this->drawUnits();
        imagepng($this->image, $frame);
        

        return $frame;

    }

    public function drawText($text, $top, $left = 0) {
        $fontPath = __DIR__ . '/font/Helvetica.ttf';
        $bbox = imagettfbbox(8, 0, $fontPath, $text);
        $textHeight = $bbox[0] - $bbox[7];
        $textWidth = $bbox[2] - $bbox[0];
        imagefilledrectangle($this->image, $left, $top, $left + $textWidth, $top + $textHeight, $this->background);
        imagettftext($this->image, 8, 0, $left, $top, $this->black, $fontPath, $text);


    }

    public function drawXY($function, $t)
    {
        //echo "$this->axes[0]->cent, $this->axes[1]->center" . PHP_EOL;
        for ($i = 0; $i < $this->width; $i++) {

            for ($j = 0; $j < $this->height; $j++) {


                $x = ($i - $this->axes[0]->center) / $this->axes[0]->pixelPerUnit;
                $y = ($this->axes[1]->center - $j) / $this->axes[1]->pixelPerUnit;
                $hex = $this->executeXY($function, $x, $y, $t);
                $this->setHexColor($hex);
                imagesetpixel($this->image, $i, $j, $this->color);

            }

        }

    }

    public function drawX($function, $hex = null, $t = 0)
    {


        if ($hex) {
            $color = $this->color;
            $this->setHexColor($hex);
        }

        $prevX = $prevY = null;
        for ($x = $this->axes[0]->min; $x < $this->axes[0]->max; $x += 1 / $this->axes[0]->pixelPerUnit) {


            $result = $this->executeX($function, $x, $t);

            if (!is_array($result)) {
                $result = [$result];
            }

            if (is_a($function, Func::class)) {

                list($xx, $xy) = $this->axes[0]->transform($x);
                list($yx, $yy) = [0,0];
     
                foreach($result as $k => $y) {
                    //echo "($x, $y) => ";
                    list($lx, $ly) = $this->axes[$k+1]->transform($y);
                    $yx +=$lx;
                    $yy +=$ly;
                    
                }

                
                $this->setHexColor($this->axes[$k+1]->color);
                $px = round($this->xToP($xx + $yx));
                $py = round($this->yToP(($yy + $xy)));
                if($prevX == null) {
                    imagesetpixel($this->image, $px, $py, $this->color);

                }
                else {
                    imageline($this->image, $prevX, $prevY, $px, $py, $this->color);
                }
                $prevX = $px;
                $prevY = $py;


            } else {

                
                list($xx, $xy) = $this->axes[0]->transform($x);

                if (true || is_a($function, Surjectif::class)) {

                    foreach ($result as $k => $y) {
                        //echo $x . ' :' . $this->xToP($x) . ' ';
                        if (is_a($function, Surjectif::class)) {
                            $axeKey = 1;
                        } else {
                            $axeKey = $k + 1;
                        }
                        list($yx, $yy) = $this->axes[$axeKey]->transform($y);
                        $this->setHexColor($this->axes[$axeKey]->color);
                        imagesetpixel($this->image, round($this->xToP($xx + $yx)), round($this->yToP(-($yy + $xy))), $this->color);

                    }
                }
            }

        }

        if ($hex) {
            $this->color = $color;
        }
    }

    public function executeXY($function, $x, $y, $t)
    {
        if (is_a($function, \Closure::class)) {
            return $function($x, $y);
        } else {

            return $function->map($x, $y, $t);
        }
    }

    public function executeX($function, $x, $t)
    {
        if (is_a($function, \Closure::class)) {
            return $function($x, $t);
        } else {

            return $function->map($x, $t);
        }
    }


    public function drawAxes()
    {

        $tickSize = 5;
        foreach ($this->axes as $k => $axis) {

            
            $start = $this->toP($axis->transform($this->pToX(0)));
            $end = $this->toP($axis->transform($this->pToX($this->width)));
            $this->setHexColor($axis->color);
            imageline($this->image, $start[0], $start[1], $end[0], $end[1], $this->color);
            
           
            $this->setHexColor("#FF0000");
            for($x = $this->axes[0]->min; $x <= $this->axes[0]->max; $x += 1) {
                //echo $x . "\n";
                $p = $this->toP($axis->transform($x));
                //echo $p[0] . ',' . $p[1] . '\n';
                imagefilledrectangle($this->image, $p[0] - 1, $p[1] - 1, $p[0]+1, $p[1], $this->color);
                
            }

            

        }

        /*imageline($this->image, 0, $this->axes[1]->center - round($this->axes[0]->cent*tan($angle)), $this->width, $this->axes[1]->center + round(($this->width - $this->axes[0]->center)*tan($angle)), $this->axisColor);

        imageline($this->image, 0, $this->axes[1]->center, $this->width, $this->axes[1]->center, $this->axisColor);
        imageline($this->image, $this->axes[0]->center, 0, $this->axes[0]->center, $this->height, $this->axisColor);

        for ($i = $this->axes[0]->center - $this->axes[0]->pixelPerUnit * floor($this->axes[0]->center / $this->axes[0]->pixelPerUnit); $i <= $this->width; $i += $this->axes[0]->pixelPerUnit) {

            $ia = round($this->xToP($this->pToX($i) * cos($angle)));

            $ya = round($this->yToP(-$this->pToX($i) * sin($angle)));
            
            imageline($this->image, $ia, $ya - $tickSize, $ia, $ya + $tickSize, $this->axisColor);
        }
        */

        /*for ($i = $this->axes[0]->center - $this->axes[0]->pixelPerUnit * floor($this->axes[0]->center / $this->axes[0]->pixelPerUnit); $i <= $this->width; $i += $this->axes[0]->pixelPerUnit) {
            imageline($this->image, round($i), $this->axes[1]->center - $tickSize, round($i), $this->axes[1]->center + $tickSize, $this->axisColor);
        }


        for ($i = $this->axes[1]->center + $this->axes[1]->pixelPerUnit * floor($this->axes[1]->center / $this->axes[1]->pixelPerUnit); $i >= 0; $i -= $this->axes[1]->pixelPerUnit) {
            imageline($this->image, round($this->axes[0]->center - $tickSize), round($i), round($this->axes[0]->center + $tickSize), round($i), $this->axisColor);
        }*/
    }

    public function drawUnits() {

        foreach($this->axes as $k => $axis) {

            //echo $k;
            /*list($x0, $y0) = $axis->location(0);
            list($x1, $y1) = $axis->location(1);
            $this->setHexColor("#000000");
            imageline($this->image, $x0, $y0, $x1, $y1, $this->color);
            */
            $x = [0,0,0];
            $x[$k] = 1;
            $this->drawLine([0,0,0], $x);
        }

    }

    public function drawLine($start, $end, $hex = '#000000') {


        list($x1, $y1) = $this->transform($start);
        list($x2, $y2) = $this->transform($end);
        //var_dump($x1, $y1, $x2, $y2);

        $this->setHexColor($hex);
        imageline($this->image, $x1, $y1, $x2, $y2, $this->color);
                

    }

    




    public function setHexColor($hex)
    {

        if (isset($this->hexCache[$hex])) {
            $this->color = $this->hexCache[$hex];
        } else {
            list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x");
            $this->color = imagecolorallocate($this->image, $r, $g, $b);
            
            $this->hexCache[$hex] = $this->color;
        }


    }

    public function setCenter($centerX, $centerY)
    {
        $this->axes[0]->center = round($centerX);
        $this->axes[1]->center = round($centerY);
    }

    public function setRanges($xMin, $xMax, $yMin, $yMax, $zMin, $zMax)
    {

        $ranges = [
            [$xMin, $xMax, $this->width],
            [$yMin, $yMax, $this->height],
            [$zMin, $zMax, $this->height]
        ];

        foreach($this->axes as $k => $axe) {

            list($min, $max, $length) = $ranges[$k];
            $axe->min = $min;
            $axe->max = $max;
            $axe->range = $max - $min; 
            //echo "$axe->range, $min, $max - ";
            $axe->pixelPerUnit = $length / $axe->range;

            

            $axe->center = $axe->toPixel(($this->axes[0]->max + $this->axes[0]->min / 2));

        }
        

    }

    public function setUnit($xUnit, $yUnit)
    {
        $this->axes[0]->pixelPerUnit = $xUnit;
        $this->axes[1]->pixelPerUnit = $yUnit;

        $this->axes[0]->range = $this->width / $this->axes[0]->pixelPerUnit;
        $this->axes[1]->range = $this->height / $this->axes[1]->pixelPerUnit;

        $this->axes[0]->min = -floor($this->axes[0]->center / $this->axes[0]->pixelPerUnit);
        $this->axes[0]->max = floor(($this->width - $this->axes[0]->center) / $this->axes[0]->pixelPerUnit);
        $this->axes[1]->min = -floor($this->axes[1]->center / $this->axes[1]->pixelPerUnit);
        $this->axes[1]->max = ($this->height - $this->axes[1]->center) / $this->axes[1]->pixelPerUnit;
        //echo "unit: ($this->axes[0]->pixelPerUnit, $this->axes[1]->pixelPerUnit), range: ($this->axes[0]->range, $this->axes[1]->range)" . PHP_EOL;
    }

    public function setUnits($units, $center = null) {

        foreach($units as $k => $unit) {
            $this->axes[$k]->pixelPerUnit = $unit;
        }

    }

    public function png($file = null)
    {

        $this->draw(0);
        return imagepng($this->image, $file);
    }



    public function save($file)
    {
        return imagepng($this->image, $file);
    }

    public function init()
    {
        //echo "ff";
        //$this->image = imagecreate($this->width, $this->height);
        imagefill($this->image, 0, 0, $this->background);
        //imagecolorallocate($this->image, 255, 255, 0);
        imagefilledrectangle($this->image, 0, 0, $this->width, $this->height, $this->background);

    }





    public function printParameters()
    {
        echo "dimension: ($this->width, $this->height)" . PHP_EOL;
        echo "center: (" . $this->axes[0]->center . ", " . $this->axes[1]->center . "), unit: (" . $this->axes[0]->pixelPerUnit . ", " . $this->axes[1]->pixelPerUnit . ")" . PHP_EOL;
        echo "minmax: [(" . $this->axes[0]->min . "," . $this->axes[0]->max . "), (" . $this->axes[1]->min . ", " . $this->axes[1]->max . ")]" . PHP_EOL;
        echo "range: ($this->axes[0]->range, $this->axes[1]->range)" . PHP_EOL;
    }


    public function pan($targetX, $targetY, $duration)
    {


        $this->motions[] = ['pan', $targetX, $targetY, $duration];
    }

    public function zoom($targetX, $targetY, $level, $duration)
    {


        $this->motions[] = ['zoom', $targetX, $targetY, $level, $duration];
    }

    public function zoom2($minX, $maxX, $minY, $maxY, $duration)
    {


        $this->motions[] = ['zoom2', $minX, $maxX, $minY, $maxY, $duration];
    }

    public function rotate($angle, $duration = null)
    {

        $this->motions[] = ['rotate', $angle, $duration];

    }

    public function still($duration)
    {
        $this->motions[] = ['still', $duration];
    }

    public function log($msg, $level = 'info')
    {
        if ($this->logger) {
            $this->logger->log($msg, $level);
        }
    }

    
    public function toP($x) {

        return [round($this->xToP($x[0])), round($this->yToP($x[1]))];
    }
    public function transform($x) {

        $px = $py = 0;
        foreach($this->axes as $k => $axe) {

            list($tr1, $tr2) = $axe->transform($x[$k]);
            $px += $tr1;
            $py += $tr2;


        }
            
        return [round($this->xToP($px)), round($this->yToP($py))];

    }

    

    public function xToP($x)
    {

        return $this->axes[0]->toPixel($x);

    }

    public function yToP($y)
    {
        return $this->axes[1]->toPixel($y);
        //return ($this->axes[1]->center - $y * $this->axes[1]->pixelPerUnit);
    }   

    public function pToX($p)
    {

        return ($p - $this->axes[0]->center) / $this->axes[0]->pixelPerUnit;

    }

    public function pToY($p)
    {
        return ($this->axes[1]->center - $p) / $this->axes[1]->pixelPerUnit;
    }


}
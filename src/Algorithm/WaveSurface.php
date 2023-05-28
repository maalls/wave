<?php

namespace Maalls\Chart\Algorithm;
use Maalls\Chart\Tool\Color;
class WaveSurface implements Func {
    
    private $tmax;
    private $zMax;

    private $distance;
    public function init($graph) {

        $this->tmax = $graph->frameCount/$graph->framePerSecond;
        $this->zMax = $graph->axes[2]->max;

        $xmin = $graph->axes[0]->min;
        $xmax = $graph->axes[0]->max;
        $this->distance = $xmax - $xmin;

        
    }
    public function map($x, $y, $t)
    {
        return sin($t/$this->tmax * 2*pi()) * cos($x/$this->distance * pi());
        //return  $t/$this->tfactor/$this->zMax;
    }

}
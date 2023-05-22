<?php 

namespace Maalls\Chart\Algorithm;

use Maalls\Chart\Tool\Color;
class Xtmap {


    public $max = 10;

    public function init($chart) {

        $this->max = $chart->xMax + $chart->yMax;
        //echo $this->max . PHP_EOL;

    }
    
    public function map($x, $y) {

        $sum = $x + $y;

        $length = 380 + ($sum)/$this->max * (780 - 380);
        $rgb = Color::waveLengthToRGB($length);
        //echo "$x, $y : $sum : $length (" . implode(',', $rgb) . ")" . PHP_EOL;
        return Color::rgbToHexa($rgb);

    }

    


}
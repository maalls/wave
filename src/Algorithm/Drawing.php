<?php 

namespace Maalls\Chart\Algorithm;
use Maalls\Chart\Chart;
interface Drawing {


    public function draw(Chart $c, $t);
}
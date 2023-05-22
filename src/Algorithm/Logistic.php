<?php 

namespace Maalls\Chart\Algorithm;
use Maalls\Chart\Algorithm\Surjectif;

class Logistic implements Surjectif {


    public function map($x) {

        $r = $x;
        //$c = new Complex($x, $y);
        //$v = new Complex(0, 0);
        $x = 0.2;

        $asymptoticValues = [];
        $n = 1000;
        for($i = 0; $i < $n; $i++) {

            $x = $r * $x * (1 - $x);

            if($x == INF) {
                break;
            }

            if($i > $n - 200) {

                if(!in_array($x, $asymptoticValues)) {
                    $asymptoticValues[] = $x;
                }

            }
        }

        return $asymptoticValues;

    }

}
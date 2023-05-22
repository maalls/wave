<?php 

use PHPUnit\Framework\TestCase;
use Maalls\Chart\Tool\MultiThread;
final class MultiThreadTest extends TestCase
{
    public function testgetNumberOfCPUs(): void
    {
        $multiThread = new MultiThread();

        $this->assertEquals(8, $multiThread->getNumberOfCPUs());
        
        
    }

    public function testRun() {
        $multiThread = new MultiThread();
        $multiThread->run(function() {
            echo "toto";
        });
    }

}
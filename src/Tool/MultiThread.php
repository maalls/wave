<?php

namespace Maalls\Chart\Tool;

class MultiThread
{

  public function __construct()
  {


  }


  public function iterate($function, $min, $max)
  {

    $count = max(1, $this->getNumberOfCPUs() - 2);
    for($i = 0; $i < $count; $i++) {
      $pid = \pcntl_fork();

      switch($pid) {
          case -1:
              print "Could not fork!\n";
              exit;
          case 0:
              
              for($t = $min + $i; $t < $max; $t += $count) {
                
                if(is_array($function)) {
                  $function[0]->{$function[1]}($t);
                }
                else {
                  $function($t);
                }
                
              }
              
              return;
          default:
              
              break;
      }
    }
    
    if($pid) {

      while (\pcntl_waitpid(0, $status) != -1) {
          $status = \pcntl_wexitstatus($status);
          
      }

     


    }
    

  }

  public function getNumberOfCPUs()
  {
    $ans = 1;
    if (is_file('/proc/cpuinfo')) {
      $cpuinfo = file_get_contents('/proc/cpuinfo');
      preg_match_all('/^processor/m', $cpuinfo, $matches);
      $ans = count($matches[0]);
    } else if ('WIN' === strtoupper(substr(PHP_OS, 0, 3))) {
      $process = @popen('wmic cpu get NumberOfCores', 'rb');
      if (false !== $process) {
        fgets($process);
        $ans = intval(fgets($process));
        pclose($process);
      }
    } else {
      $ps = @popen('sysctl -a', 'rb');
      if (false !== $ps) {
        $output = stream_get_contents($ps);
        preg_match('/hw.ncpu: (\d+)/', $output, $matches);
        if ($matches) {
          $ans = intval($matches[1][0]);
        }
        pclose($ps);
      }
    }
    return trim($ans);
  }



}
<?php
/**
 * Command like Metatag writer for video files.
 */

namespace CWPCLI\Traits;

use Nette\Utils\Strings;
use Symfony\Component\Process\Process;

trait Callables
{
    public function Output($type, $buffer)
    {
        echo $buffer;
    }


    public function ProcessOutput($type, $buffer)
    {
        if (Process::ERR === $type) {
            echo 'ERR > '.$buffer;
        } else {
            echo 'OUT > '.$buffer;
        }
    }


}

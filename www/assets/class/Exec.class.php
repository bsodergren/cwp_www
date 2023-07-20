<?php
/**
 * CWP Media tool
 */

use Nette\Utils\Callback;
use Nette\Utils\FileSystem;
use Symfony\Component\Process\Process;

class exec
{
    public $executable;
    public $optArray = [];

    // public $optArray = [];

    public $cmdArgs  = [];

    public function __construct($exec)
    {
        $this->cmdArgs    = [];
        $this->executable =  FileSystem::normalizePath($exec);
    }

    public function option($arg, $value = null)
    {
        $this->optArray[] = ['opt' => $arg, 'value' => $value];
    }

    private function createCmd()
    {
        $this->cmdArgs[] =  $this->executable;
        foreach ($this->optArray as $k => $arg) {
            $this->cmdArgs[] = $arg['opt'];
            if (null !== $arg['value']) {
                $this->cmdArgs[] = $arg['value'];
            }
        }
    }

    public function run()
    {
        $callback            = Callback::check([$this, 'callback']);

        $this->createCmd();

        $process             = new Process($this->cmdArgs);
        $process->setTimeout(60000);

        $runCommand          = $process->getCommandLine();

        $process->start();
        $process->wait($callback);
    }

    public function callback($type, $buffer): void
    {
        if (Process::ERR === $type) {
            // HTMLDisplay::put('ERR > '.$buffer.'<br>', 'red');
        } else {
            // HTMLDisplay::put('OUT > '.$buffer.'<br>', 'green');
        }
    }
}

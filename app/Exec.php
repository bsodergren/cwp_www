<?php
namespace CWP;
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

    private object $ExecProcess;

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
        $this->getCommand();

        $callback            = Callback::check([$this, 'callback']);

        $this->ExecProcess->start();
        $this->ExecProcess->wait($callback);
    }

    public function getCommand()
    {
        $this->createCmd();
        $this->ExecProcess             = new Process($this->cmdArgs);
        $this->ExecProcess->setTimeout(60000);

        return $this->ExecProcess->getCommandLine();

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

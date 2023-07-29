<?php
/**
 * CWP Media tool
 */

namespace CWP\Media;

/*
 * CWP Media tool
 */

use CWP\HTML\HTMLDisplay;
use Nette\Utils\Callback;
use Nette\Utils\FileSystem;
use Symfony\Component\Process\Process;

class MediaProcess
{
    public $executable;
    public $optArray = [];

    private object $ExecProcess;

    // public $optArray = [];

    public $cmdArgs = [];

    public function __construct()
    {
        $this->cmdArgs = [];
    }

    public function command($exec)
    {
        $this->executable = FileSystem::normalizePath($exec);
    }

    public function option($arg, $value = null)
    {
        $this->optArray[] = ['opt' => $arg, 'value' => $value];
    }

    private function createCmd()
    {
        $this->cmdArgs[] = $this->executable;
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

        $callback = Callback::check([$this, 'callback']);

        $this->ExecProcess->start();
        $this->ExecProcess->wait($callback);
    }

    public function getCommand()
    {
        $this->createCmd();
        $this->ExecProcess = new Process($this->cmdArgs);
        $this->ExecProcess->setTimeout(60000);

        return $this->ExecProcess->getCommandLine();
    }

    public function cleanPdf($pdf_file)
    {
        $qdf_cmd = FileSystem::normalizePath(__BIN_DIR__.'/qpdf');
        $pdf_file = FileSystem::normalizePath($pdf_file);
        HTMLDisplay::put('Waiting for PDF for finish');

        $this->command($qdf_cmd);
        $this->option($pdf_file);
        $this->option('--pages', '.');
        $this->option('1-z', '--');
        $this->option('--replace-input');
        //  dd($process->getCommand());
        $this->run();
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

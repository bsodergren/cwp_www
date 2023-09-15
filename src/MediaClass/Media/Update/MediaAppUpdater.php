<?php

namespace CWP\Media\Update;

use CWP\HTML\HTMLDisplay;
use Nette\Utils\Callback;
use Nette\Utils\FileSystem;
use Symfony\Component\Process\Process;

class MediaAppUpdater
{

    public const GIT_VERSION = "https://raw.githubusercontent.com/bsodergren/cwp_www/main/current.txt";

    public $latest;
    public $current;

    public function __construct()
    {
        define('__UPDATE_CURRENT_FILE__', FileSystem::normalizePath(__PUBLIC_ROOT__.'/current.txt'));
        $this->getLastest();
        $this->currentVersion();
    }

    public function get_content($URL)
    {
        $ch = curl_init();
        curl_setopt($ch, \CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, \CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, \CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, \CURLOPT_URL, $URL);
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
    public function currentVersion()
    {
        $this->current = trim(file_get_contents(__UPDATE_CURRENT_FILE__));

    }
    public function getLastest()
    {
        $this->latest = trim($this->get_content(self::GIT_VERSION));
    }

    public function isUpdate()
    {

        if($this->latest > $this->current){
            return $this->latest;
        }
        return null;
    }

    public function callback($type, $buffer): void
    {
        if (Process::ERR === $type) {
             HTMLDisplay::put('ERR > '.$buffer.'<br>', 'red');
        } else {
             HTMLDisplay::put('OUT > '.$buffer.'<br>', 'green');
        }
    }
    public function getUpdate()
    {
        $ExecProcess = new Process(['git','pull']);
        $callback = Callback::check([$this, 'callback']);
        $ExecProcess->start();
        $ExecProcess->wait($callback);
    }
}

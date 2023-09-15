<?php

namespace CWP\Updater;

use CWP\Core\MediaSettings;
use CWP\Media\MediaExec;
use CWP\HTML\HTMLDisplay;
use Nette\Utils\Callback;
use Nette\Utils\FileSystem;
use Symfony\Component\Process\Process;

class MediaAppUpdater
{
    public const GIT_VERSION = "https://raw.githubusercontent.com/bsodergren/cwp_www/main/current.txt";

    public $latest;
    public $current;
    public $process;

    public function __construct()
    {
        define('__UPDATE_CURRENT_FILE__', FileSystem::normalizePath(__PUBLIC_ROOT__.'/current.txt'));
        $this->getLastest();
        $this->currentVersion();
        $this->process = new MediaExec();

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
        if(__DEBUG__ == true) {
            $this->current = '1.2.3';
        } else {
            $this->current = trim(file_get_contents(__UPDATE_CURRENT_FILE__));
        }

    }
    public function getLastest()
    {
        $this->latest = trim($this->get_content(self::GIT_VERSION));
    }

    public function isUpdate()
    {

        if($this->latest > $this->current) {
            return $this->latest;
        }
        return null;
    }

    public function callback($type, $buffer): void
    {

        HTMLDisplay::put(nl2br($buffer), 'green');

    }
    public function getUpdate()
    {
        $callback = Callback::check([$this, 'callback']);
        $this->process->command('git');
        $this->process->option('pull');
        if(__DEBUG__ == true) {
            echo  $this->process->getCommand();
        } else {
            $this->process->run($callback);
        }
    }

    public function composerUpdate()
    {
        $callback = Callback::check([$this, 'callback']);

        $this->process->command('composer');
        $this->process->option('-d');
        $this->process->option(__PUBLIC_ROOT__);
        $this->process->option('update');
        if(__DEBUG__ == true) {
            echo  $this->process->getCommand();
        } else {
            $this->process->exec($callback, ['HOME' => __HOME__]);
        }
    }
}

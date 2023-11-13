<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWP\Updater;

use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaExec;
use Nette\Utils\Callback;
use Nette\Utils\FileSystem;

class MediaAppUpdater
{
    public const GIT_VERSION = 'https://raw.githubusercontent.com/bsodergren/cwp_www/main/current.txt';

    public $latest;

    public $current;

    public $process;

    public function __construct()
    {
        if (!\defined('__UPDATE_CURRENT_FILE__')) {
            \define('__UPDATE_CURRENT_FILE__', FileSystem::normalizePath(__PUBLIC_ROOT__.'/current.txt'));
        }
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
        if (__DEBUG__ == true) {
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
        if ($this->latest > $this->current) {
            return $this->latest;
        }

        return null;
    }

    public function callback($type, $buffer): void
    {
        HTMLDisplay::put(nl2br($buffer), 'black', false);
    }

    public function getUpdate()
    {
        $process = new MediaExec();
        $callback = Callback::check([$this, 'callback']);
        $process->command('git');
        $process->option('pull');
        if (__DEBUG__ == true) {
            echo $process->getCommand();
        } else {
            $process->run($callback);
        }
    }

    public function composerUpdate()
    {
        $callback = Callback::check([$this, 'callback']);
        $process = new MediaExec();

        $process->command('composer');
        $process->option('-d');
        $process->option(__PUBLIC_ROOT__);
        $process->option('update');
        $process->option('--no-cache');
        $process->option('--no-ansi');
        if (__DEBUG__ == true) {
            echo $process->getCommand();
        } else {
            $process->exec($callback, ['HOME' => __HOME__]);
        }
    }
}

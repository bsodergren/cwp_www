<?php
/**
 * CWP Media tool
 */

namespace CWP\Media\Update;

use CWP\HTML\HTMLDisplay;
use CWP\Media\MediaExec;

class AppUpdate extends MediaUpdate
{
    public $gitRaw = 'https://raw.githubusercontent.com/bsodergren/cwp_www/main/www/updater/';
    public $updateUrl;
    public $versionUrl;
    public $zip_url;
    public $installed;

    public $updateFiles = [];

    public $VersionUpdates = [];

    public $conf = [];

    //  public $builder_exec       = __ROOT_BIN_DIR__.\DIRECTORY_SEPARATOR.'builder.exe';
    public $patcher_exec = __BIN_DIR__.\DIRECTORY_SEPARATOR.'patcher.exe';
    public static $UPDATES_PENDING;
    public static $CURRENT_VERSION;

    public function init()
    {
        if (__NO_UPDATES__ === false) {
            $this->updateUrl = $this->gitRaw.'current.txt';
            $this->versionUrl = $this->gitRaw.'version.txt';
            $this->zip_url = $this->gitRaw.'versions/';

            $current = trim($this->get_content($this->updateUrl));
            self::$CURRENT_VERSION = $current;
            $this->installed = trim(file_get_contents(__UPDATE_CURRENT_FILE__));
            self::$UPDATES_PENDING = false;
            if ($current > $this->installed) {
                self::$UPDATES_PENDING = $this->getNumUpdates();
            }
        } else {
            self::$UPDATES_PENDING = false;
        }
    }

    public function currentVersion()
    {
        return $this->installed;
    }

    public function getNumUpdates()
    {
        $this->getUpdates();

        return count($this->VersionUpdates);
    }

    public function getUpdates()
    {
        $allVersions = $this->get_content($this->versionUrl);
        $verArray = explode("\n", $allVersions);

        $installed = str_replace('.', '', $this->installed);
        foreach ($verArray as $Updates) {
            $Updates = trim($Updates);
            $UpdatesNum = str_replace('.', '', $Updates);
            if ($installed >= $UpdatesNum) {
                continue;
            }
            $this->VersionUpdates[] = $Updates;
        }
    }

    public function getUpdateFiles()
    {
        foreach ($this->VersionUpdates as $version) {
            $zip_filename = 'update_'.$version.'.zip';
            $zip_dl_url = $this->zip_url.$zip_filename;
            $data = $this->get_content($zip_dl_url);

            if (!is_dir(__VERSION_DL_DIR__)) {
                mkdir(__VERSION_DL_DIR__, 0777, true);
            }

            $destination = __VERSION_DL_DIR__.\DIRECTORY_SEPARATOR.$zip_filename;
            $this->updateFiles[] = $destination;
            if (file_exists($destination)) {
                unlink($destination);
            }
            $file = fopen($destination, 'w+');
            fwrite($file, $data);
            fclose($file);
        }
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

    public function doUpdates()
    {
        foreach ($this->updateFiles as $updateFile) {
            HTMLDisplay::put('Writing '.basename($updateFile), 'red');
            $process = new MediaExec();
            $process->command($this->patcher_exec);
            $process->option('-O', __PROJECT_ROOT__);
            $process->option('-P', $updateFile);
            $process->run();
        }
    }
}

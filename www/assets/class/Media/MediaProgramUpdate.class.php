<?php
/**
 * CWP Media tool
 */

use Symfony\Component\Process\Process;

class MediaProgramUpdate
{
    public $gitRaw             = 'https://raw.githubusercontent.com/bsodergren/cwp_www/main/www/updater/';
    public $updateUrl;
    public $versionUrl;
    public $zip_url;

    public $updateFiles        = [];

    public $doUpdates          = [];

    public $conf               = [];

    //  public $builder_exec       = __ROOT_BIN_DIR__.\DIRECTORY_SEPARATOR.'builder.exe';
    public $patcher_exec       = __ROOT_BIN_DIR__.\DIRECTORY_SEPARATOR.'patcher.exe';
    public static $UPDATES_PENDING;

    public function __construct()
    {
        global $conf;
        $this->conf               = $conf;
        $this->updateUrl          = $this->gitRaw.'current.txt?432=432';
        $this->versionUrl         = $this->gitRaw.'version.txt?432=432';
        $this->zip_url            = $this->gitRaw.'versions/';

        $current                  = trim($this->get_content($this->updateUrl));
        $installed                = trim(file_get_contents(__VERSION_FILE__));
        self::$UPDATES_PENDING    = false;
        if ($current > $installed) {
            self::$UPDATES_PENDING = $this->getNumUpdates();
        }
    }

    public function getNumUpdates()
    {
        $this->getUpdates();

        return count($this->doUpdates());
    }

    public function getUpdates()
    {
        $allVersions         = $this->get_content($this->versionUrl);
        $verArray            = explode("\n", $allVersions);

        foreach ($verArray as $Updates) {
            if ($this->installed >= trim($Updates)) {
                continue;
            }
            $this->doUpdates[]    = trim($Updates);
        }
    }

    public function getUpdateFiles()
    {
        foreach ($this->doUpdates as $version) {
            $zip_filename           = 'update_'.$version.'.zip';
            $zip_dl_url             = $this->zip_url.$zip_filename;
            $data                   = $this->get_content($zip_dl_url);

            if (!is_dir(__VERSION_DL_DIR__)) {
                mkdir(__VERSION_DL_DIR__, 0777, true);
            }

            $destination            = __VERSION_DL_DIR__.\DIRECTORY_SEPARATOR.$zip_filename;
            $this->updateFiles[]    = $destination;
            if (file_exists($destination)) {
                unlink($destination);
            }
            $file                   = fopen($destination, 'w+');
            fwrite($file, $data);
            fclose($file);
        }
    }

    public function get_content($URL)
    {
        $ch   = curl_init();
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
            echo 'Running update on '.basename($updateFile).'<br>';

            $command             = [
                $this->patcher_exec,
                '-O',
                __DRIVE_LETTER__.$this->conf['server']['root_dir'],
                '-P',
                $updateFile,
            ];

            $process             = new Process($command);
            $process->setTimeout(60000);

            // $runCommand = $process->getCommandLine();

            $process->run(function ($type, $buffer): void {
                if (Process::ERR === $type) {
                    echo 'ERR > '.$buffer.'<br>';
                } else {
                    echo 'OUT > '.$buffer.'<br>';
                }
            });
            // $process->wait();
        }
    }
}

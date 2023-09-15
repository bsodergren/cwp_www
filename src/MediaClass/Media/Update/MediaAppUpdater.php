<?php

namespace CWP\Media\Update;

use Nette\Utils\FileSystem;

class MediaAppUpdater
{

    public const GIT_VERSION = "https://raw.githubusercontent.com/bsodergren/cwp_www/main/current.txt";

    public function __construct()
    {
        define('__UPDATE_CURRENT_FILE__', FileSystem::normalizePath(__PUBLIC_ROOT__.'/current.txt'));

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
        $installed = trim(file_get_contents(__UPDATE_CURRENT_FILE__));
        return $installed;
    }
    public function getLastest(){

        echo $this->get_content(self::GIT_VERSION);

    }
}

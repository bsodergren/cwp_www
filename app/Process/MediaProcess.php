<?php
/**
 * CWP Media tool
 */

namespace CWP\Process;

use CWP\HTML\HTMLDisplay;

class MediaProcess
{
    public object $media;
    public $job_id;

    public $url = "/index.php";
    public $msg = "";
    public $timeout = "0";

    public function __construct($media)
    {
        if (is_object($media)) {
            $this->media = $media;
            $this->job_id = $media->job_id;
        }
    }

    public function run($req)
    {
        $class = get_called_class();
        (new $class())->run($req);
    }

    public function reload()
    {
        echo HTMLDisplay::JavaRefresh($this->url, $this->timeout, $this->msg);
    }
}

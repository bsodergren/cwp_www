<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem\Driver;

interface MediaFileInterface
{
    public function getContents($path);

    public function exists($path);

    public function rename($old, $new);

    public function delete($path);

    public function createFolder($path);

    public function dirExists($directory);

    public function search($path, $search);

    public function getFile($file);

    public function uploadFile($filename, $dropboxFilename, $options = []);

    public function DownloadFile($filename);

    public function postSaveFile($file);
}

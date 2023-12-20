<?php
namespace CWP\Filesystem\Driver;
use Nette\Utils\FileSystem;

class MediaFS
{
    public $directory;

    public $pdf_file;

    public $job_number;
    public $tmpDirectory = false;

    public function save($localfile, $remotefile, $options = [])
    {

    }

    public function downloadXLSXFiles($xlsDir)
    {

    }

    public function getXLSXDir($xlsDir)
    {
        return $this->path($xlsDir,true);
    }

    public function getZipFile($zipFile, $path)
    {
        return $this->path($path.\DIRECTORY_SEPARATOR.'zip'.\DIRECTORY_SEPARATOR.basename($zipFile), true);
    }


    public function path($path, $create = false)
    {
        $path = Filesystem::normalizePath($path);
        $path = Filesystem::platformSlashes($path);

        if (true == $create) {
            if (is_dir($path)) {
                $dir = $path;
            } else {
                $dir = \dirname($path);
            }
            FileSystem::createDir($dir);
        }

        return $path;
    }
}
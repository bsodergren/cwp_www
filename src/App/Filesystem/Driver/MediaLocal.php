<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem\Driver;

use CWP\Filesystem\MediaFileSystem;
use CWP\Filesystem\MediaFinder;
use Nette\InvalidStateException;
use Nette\Utils\FileSystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

class MediaLocal implements MediaFileInterface
{
    public $directory;

    public $pdf_file;

    public $job_number;

    public function getContents($path)
    {
        $f     = new MediaFinder();
        $array = $f->search($path, '*.pdf');
        foreach ($array as $file) {
            $return[] = ['name' => basename($file), 'path' => $file];
        }

        return $return;
    }

    public function exists($file)
    {
        $directory = (new MediaFileSystem())->directory('upload', true);
        $pdf_file  = $directory.\DIRECTORY_SEPARATOR.$file;

        return file_exists($pdf_file);
    }

    public function delete($file)
    {
        $msg = null;

        if (file_exists($file) || is_dir($file)) {
            try {
                FileSystem::delete($file);
            } catch (IOException $e) {
                $msg = $e->getMessage();
            }
        } else {
            $msg = $file.' not found';
        }

        return $msg;
    }

    public function rename($old, $new)
    {
        $msg = null;
        $old = FileSystem::platformSlashes($old);

        $new = FileSystem::platformSlashes($new);

        try {
            if (false == FileSystem::rename($old, $new)) {
                throw new InvalidStateException();
            }
        } catch (InvalidStateException $e) {
            $msg = $e->getMessage();
        }

        return $msg;
    }

    public function createFolder($path)
    {
    }

    public function uploadFile($filename, $dropboxFilename, $options = [])
    {
        $this->rename($filename, $dropboxFilename);
    }

    public function DownloadFile($filename)
    {
        return $filename;
    }

    public function dirExists($dir)
    {
        return is_dir($dir);
    }

    public function search($search, $path = '/')
    {
        $finder = new Finder();
        $finder->files()->in($path)->name($search)->notName('~*')->sortByName(true);
        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        return $files;
    }

    public function getFile($filename)
    {
        return $filename;
    }
}

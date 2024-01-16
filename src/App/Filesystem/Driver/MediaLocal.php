<?php
/**
 * CWP Media tool for load flags.
 */

namespace CWP\Filesystem\Driver;

use CWP\Filesystem\MediaFileSystem;
use CWP\Filesystem\MediaFinder;
use Nette\Utils\FileSystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\Finder;

class MediaLocal extends MediaFS implements MediaFileInterface
{
    public function postSaveFile($postFileArray, $pdf = true)
    {
        $fileName = $postFileArray['name'];

        $fileTmpName = $postFileArray['tmp_name'];
        $pdf_directory = __FILES_DIR__;
        if (true === $pdf) {
            // $loc = new MediaFileSystem();
            $pdf_directory = (new MediaFileSystem())->getDirectory('upload', false);
        }

        $pdf_file = $pdf_directory.\DIRECTORY_SEPARATOR.basename($fileName);
        // $this->delete($pdf_file);

        $fileTmpName = FileSystem::platformSlashes($fileTmpName);
        $pdf_file = FileSystem::platformSlashes($pdf_file);

        $res = move_uploaded_file($fileTmpName, $pdf_file);
        // $loc->UploadFile($fileTmpName, $pdf_file, ['autorename' => false]);

        return $pdf_file;
    }

    public function getContents($path, $ext = '*.pdf')
    {
        $f = new MediaFinder();
        $array = $f->search($path, $ext);
        foreach ($array as $file) {
            $return[] = $file;
        }

        return $return;
    }

    public function exists($file)
    {
        return file_exists($file);
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
                throw new \Nette\InvalidStateException();
            }
        } catch (\Nette\InvalidStateException $e) {
            $msg = $e->getMessage();
        }

        return $msg;
    }

    public function copy($old, $new, $overwrite = true)
    {
        $msg = null;
        $old = FileSystem::platformSlashes($old);

        $new = FileSystem::platformSlashes($new);

        try {
            if (false == FileSystem::copy($old, $new, $overwrite)) {
                throw new \Nette\InvalidStateException();
            }
        } catch (\Nette\InvalidStateException $e) {
            $msg = $e->getMessage();
        }

        return $msg;
    }

    public function createFolder($path)
    {
        FileSystem::createDir($path);
    }

    public function uploadFile($filename, $pdf_file, $options = [])
    {
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

    public function write($remotefile, $contents)
    {
        FileSystem::write($remotefile, $contents);
    }
}

<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem;

use CWP\Core\Media;
use CWP\Filesystem\Driver\MediaDropbox;
use CWP\Filesystem\Driver\MediaGoogleDrive;
use CWP\Filesystem\Driver\MediaLocal;
use Nette\Utils\FileSystem;

class MediaFinder
{
    public $fileDriver;
    public $media;

    public function __construct($media = '')
    {
        $this->media = $media;
        $this->fileDriver = Media::getFileDriver();

    }

    public function dirExists($directory)
    {
        return $this->fileDriver->dirExists($directory);
    }

    public function search($path, $search)
    {
        $path = FileSystem::unixSlashes($path);

        return $this->fileDriver->search($search, $path);
    }

    public function getFile($file)
    {
        return $this->fileDriver->getFile($file);
    }
}

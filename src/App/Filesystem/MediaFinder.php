<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem;

use CWP\Filesystem\Driver\MediaDropboxFinder;
use CWP\Filesystem\Driver\NetteFinder;
use Nette\Utils\FileSystem;

class MediaFinder
{
    public $finder;

    public function __construct($media)
    {
        $this->media = $media;

        if (__USE_DROPBOX__ == true) {
            $this->finder = new MediaDropboxFinder();
        } else {
            $this->finder = new NetteFinder();
        }
    }

    public function dirExists($directory)
    {
        return $this->finder->is_dir($directory);
    }

    public function search($path, $search)
    {
        $path = FileSystem::unixSlashes($path);

        return $this->finder->search($search, $path);
    }

    public function getFile($file)
    {
        return $this->finder->getFile($file);
    }
}

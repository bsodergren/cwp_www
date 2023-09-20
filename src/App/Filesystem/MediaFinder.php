<?php

namespace CWP\Filesystem;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;
use CWP\Filesystem\Driver\NetteFinder;
use CWP\Filesystem\Driver\MediaDropboxFinder;

class MediaFinder
{

    public $finder;

    public function __construct($media)
    {
        $this->media = $media;

        if (__USE_DROPBOX__ == true )
        {
            $this->finder = new MediaDropboxFinder();
        } else {
            $this->finder = new NetteFinder();
        }
    }

    public function dirExists($directory)
    {
        return $this->finder->is_dir($directory);

    }

    public function search($path,$search)
    {
        $path = FileSystem::unixSlashes($path);
        return $this->finder->search($path,$search );
    }

    public function getFile($file){
        return $this->finder->getFile($file);
    }

}

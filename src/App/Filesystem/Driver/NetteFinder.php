<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Filesystem\Driver;

use Symfony\Component\Finder\Finder;

class NetteFinder extends Finder
{
    public function is_dir($dir)
    {
        return is_dir($dir);
    }

    public function search($path, $search)
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

<?php

namespace CWP\Filesystem\Driver;
use Nette\Utils\Finder;

class NetteFinder extends Finder
{
    public function is_dir($dir)
    {
        return is_dir($dir);
    }

    public function search($path,$search)
    {
        $finder = new Finder();
       return $finder->files()->in($path)->name($search)->notName('~*')->sortByName(true);

    }
}

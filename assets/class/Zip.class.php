<?php

use Nette\Utils\FileSystem;


// The Class
class AdvancedFilesystemIterator extends ArrayIterator
{
    public function __construct(string $path, int $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS)
    {
        parent::__construct(iterator_to_array(new FilesystemIterator($path, $flags)));
    }

    public function __call(string $name, array $arguments)
    {
        if (preg_match('/^sortBy(.*)/', $name, $m)) return $this->sort('get' . $m[1]);
        throw new MemberAccessException('Method ' . $methodName . ' not exists');
    }

    public function sort($method)
    {
        if (!method_exists('SplFileInfo', $method)) throw new InvalidArgumentException(sprintf('Method "%s" does not exist in SplFileInfo', $method));

        $this->uasort(function (SplFileInfo $a, SplFileInfo $b) use ($method) {
            return (is_string($a->$method()) ? strnatcmp($a->$method(), $b->$method()) : $b->$method() - $a->$method());
        });

        return $this;
    }

    public function limit(int $offset = 0, int $limit = -1)
    {
        return parent::__construct(iterator_to_array(new LimitIterator($this, $offset, $limit))) ?? $this;
    }

    public function match(string $regex, int $mode = RegexIterator::MATCH, int $flags = 0, int $preg_flags = 0)
    {
        return parent::__construct(iterator_to_array(new RegexIterator($this, $regex, $mode, $flags, $preg_flags))) ?? $this;
    }
}


class zip_Workbooks
{
    public function __construct($xlsx_directory, $job_id, $zip_file)
    {
        global $explorer;

        $rootPath = realpath($xlsx_directory);
        $zipPath = str_replace(basename($zip_file), "", $zip_file);

        FileSystem::createDir($zipPath);

        $zip = new ZipArchive();
        $zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = (new AdvancedFilesystemIterator($rootPath))->sortByMTime();


        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        // Zip archive will be created only after closing object
        $zip->close();

        $explorer->table('media_job')->where('job_id', $job_id)->update(['zip_exists' => '1']);


        //myHeader($_SERVER['REQUEST_URI']);
    }
}

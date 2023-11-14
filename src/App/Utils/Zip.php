<?php
/**
 * CWP Media Load Flag Creator
 */

namespace CWP\Utils;

use CWP\Core\Media;
use Nette\Utils\FileSystem;

// The Class
class AdvancedFilesystemIterator extends \ArrayIterator
{
    public function __construct(string $path, int $flags = \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS)
    {
        parent::__construct(iterator_to_array(new \FilesystemIterator($path, $flags)));
    }

    public function __call(string $name, array $arguments)
    {
        if (preg_match('/^sortBy(.*)/', $name, $m)) {
            return $this->sort('get'.$m[1]);
        }
        throw new MemberAccessException('Method '.$methodName.' not exists');
    }

    public function sort($method)
    {
        if (!method_exists('SplFileInfo', $method)) {
            throw new \InvalidArgumentException(sprintf('Method "%s" does not exist in SplFileInfo', $method));
        }

        $this->uasort(function (\SplFileInfo $a, \SplFileInfo $b) use ($method) {
            return \is_string($a->{$method}()) ? strnatcmp($a->{$method}(), $b->{$method}()) : $b->{$method}() - $a->{$method}();
        });

        return $this;
    }

    public function limit(int $offset = 0, int $limit = -1)
    {
        return parent::__construct(iterator_to_array(new \LimitIterator($this, $offset, $limit))) ?? $this;
    }

    public function match(string $regex, int $mode = \RegexIterator::MATCH, int $flags = 0, int $preg_flags = 0)
    {
        return parent::__construct(iterator_to_array(new \RegexIterator($this, $regex, $mode, $flags, $preg_flags))) ?? $this;
    }
}

class Zip
{
    public $xlsx_dir;
    public $zip_file;
    public $job_id;

    public $remote_xlsx;
    public $remote_zip;

    public object $driver;

    public function __construct($object)
    {
        $this->remote_xlsx = $object->media->xlsx_directory;
        $this->remote_zip = $object->media->zip_file;
        // $this->zip_file  = $object->media->zip_file;
        $this->job_id = $object->job_id;

        $this->driver = Media::getFileDriver();

        $this->xlsx_dir = $this->driver->getXLSXDir($this->remote_xlsx);

        $this->zip_file = $this->driver->getZipFile($object->media->zip_file, \dirname($this->xlsx_dir));

        // dd($this->xlsx_dir, $this->zip_file);
    }

    public function zip()
    {
        // $zipPath  = dirname($this->zip_file);

        $this->driver->downloadXLSXFiles($this->remote_xlsx);

        $zip = new \ZipArchive();
        $zip->open($this->zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $files = (new AdvancedFilesystemIterator($this->xlsx_dir))->sortByMTime();

        foreach ($files as $name => $file) {
            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, \strlen($this->xlsx_dir) + 1);
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $d = $zip->close();

        if (true === $d) {
            $this->driver->save($this->zip_file, $this->remote_zip);
            $table = Media::$explorer->table('media_job')->where('job_id', $this->job_id)->update(['zip_exists' => 1]);
            FileSystem::delete(\dirname($this->xlsx_dir, 1));
            return 'Zip file created';


        }

        return 'zip file not created, probably a file open';
    }

    public function exportZip($pdf_file, $jsonFile)
    {
        $zipPath = \dirname($pdf_file, 2).'/Export';
        FileSystem::createDir($zipPath);

        $zip_file = $zipPath.'/export.zip';
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $zip->addFile($pdf_file, basename($pdf_file));
        $zip->addFile($jsonFile, basename($jsonFile));

        $d = $zip->close();
    }
}

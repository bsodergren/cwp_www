<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Cache;

use CWP\Cache\Exception\InvalidArgumentException;
use CWP\Cache\Option\FilenameTrait as FilenameOption;

/**
 * Abstract class for using files as cache.
 */
abstract class AbstractFile extends AbstractCache
{
    use FilenameOption;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * Class constructor.
     */
    public function __construct(string $cacheDir = null)
    {
        if (!$cacheDir) {
            $cacheDir = realpath(sys_get_temp_dir()).\DIRECTORY_SEPARATOR.'cache';
        }

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        $this->cacheDir = rtrim($cacheDir, '/');
    }

    /**
     * Validate the key.
     *
     * @param string $key
     *
     * @throws InvalidArgumentException
     */
    protected function assertKey($key): void
    {
        parent::assertKey($key);

        if (strpos($key, '*')) {
            throw new InvalidArgumentException("Key may not contain the character '*'");
        }
    }

    /**
     * Get the contents of the cache file.
     */
    protected function readFile(string $cacheFile): string
    {
        return file_get_contents($cacheFile);
    }

    /**
     * Read the first line of the cache file.
     */
    protected function readLine(string $cacheFile): string
    {
        $fp = fopen($cacheFile, 'r');
        $line = fgets($fp);
        fclose($fp);

        return $line;
    }

    /**
     * Create a cache file.
     */
    protected function writeFile(string $cacheFile, string $contents): bool
    {
        $dir = \dirname($cacheFile);

        if ($dir !== $this->cacheDir && !is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return (bool) file_put_contents($cacheFile, $contents);
    }

    /**
     * Delete a cache file.
     */
    protected function deleteFile(string $file): bool
    {
        return !is_file($file) || unlink($file);
    }

    /**
     * Remove all files from a directory.
     */
    protected function removeFiles(string $dir): bool
    {
        $success = true;

        $generator = $this->getFilenameOption();
        $objects = $this->streamSafeGlob($dir, $generator('*'));

        foreach ($objects as $object) {
            $success = $this->deleteFile($object) && $success;
        }

        return $success;
    }

    /**
     * Recursive delete an empty directory.
     */
    protected function removeRecursively(string $dir): bool
    {
        $success = $this->removeFiles($dir);

        $objects = $this->streamSafeGlob($dir, '*');

        foreach ($objects as $object) {
            if (!is_dir($object)) {
                continue;
            }

            if (is_link($object)) {
                unlink($object);
            } else {
                $success = $this->removeRecursively($object) && $success;
                rmdir($object);
            }
        }

        return $success;
    }

    public function delete(string $key): bool
    {
        $cacheFile = $this->getFilename($key);

        return $this->deleteFile($cacheFile);
    }

    /**
     * Delete cache directory.
     *
     * {@inheritdoc}
     */
    public function clear(): bool
    {
        $this->removeRecursively($this->cacheDir);

        return true;
    }

    /**
     * Glob that is safe with streams (vfs for example).
     */
    protected function streamSafeGlob(string $directory, string $filePattern): array
    {
        $filePattern = basename($filePattern);
        $files = scandir($directory);
        $found = [];

        foreach ($files as $filename) {
            if (\in_array($filename, ['.', '..'])) {
                continue;
            }

            if (fnmatch($filePattern, $filename) || fnmatch($filePattern.'.ttl', $filename)) {
                $found[] = "{$directory}/{$filename}";
            }
        }

        return $found;
    }
}

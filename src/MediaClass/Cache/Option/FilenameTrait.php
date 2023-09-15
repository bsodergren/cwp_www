<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Option;

use CWP\Cache\File\BasicFilename;

/**
 * Use filename generator.
 */
trait FilenameTrait
{
    /**
     * @var callable
     */
    protected $filename;

    /**
     * Filename format or callable.
     * The filename format will be applied using sprintf, replacing `%s` with the key.
     *
     * @param string|callable $filename
     */
    protected function setFilenameOption($filename): void
    {
        if (\is_string($filename)) {
            $filename = new BasicFilename($filename);
        }

        if (!\is_callable($filename)) {
            throw new \TypeError('Filename should be a string or callable');
        }

        $this->filename = $filename;
    }

    /**
     * Get the filename callable.
     */
    protected function getFilenameOption(): callable
    {
        if (!isset($this->filename)) {
            $this->filename = new BasicFilename('%s.'.$this->getPacker()->getType());
        }

        return $this->filename;
    }

    /**
     * Create a filename based on the key.
     *
     * @param string|mixed $key
     */
    protected function getFilename($key): string
    {
        $id = $this->keyToId($key);
        $generator = $this->getFilenameOption();

        return $this->cacheDir.\DIRECTORY_SEPARATOR.$generator($id);
    }

    /**
     * Get a wildcard for all files.
     */
    protected function getWildcard(): string
    {
        $generator = $this->getFilenameOption();

        return $this->cacheDir.\DIRECTORY_SEPARATOR.$generator('');
    }
}

<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\File;

/**
 * Create a path for a key as prefix tree directory structure.
 *
 * @see https://en.wikipedia.org/wiki/Trie
 */
class TrieFilename
{
    /**
     * @var string
     */
    protected $format;

    /**
     * @var int
     */
    protected $levels;

    /**
     * @var bool
     */
    protected $hash;

    /**
     * TrieFilename constructor.
     *
     * @param int  $levels The depth of the structure
     * @param bool $hash   MD5 hash the key to get a better spread
     */
    public function __construct(string $format, int $levels = 1, bool $hash = false)
    {
        $this->format = $format;
        $this->levels = $levels;
        $this->hash = $hash;
    }

    /**
     * Get the format.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get the depth of the structure.
     */
    public function getLevels(): int
    {
        return $this->levels;
    }

    /**
     * Will the key be hashed to create the trie.
     */
    public function isHashed(): bool
    {
        return $this->hash;
    }

    /**
     * Create the path for a key.
     */
    public function __invoke(string $key): string
    {
        if (empty($key)) {
            return $this->wildcardPath();
        }

        $dirname = $this->hash ? base_convert(md5($key), 16, 36) : $key;
        $filename = sprintf($this->format, $key);

        $path = '';

        for ($length = 1; $length <= $this->levels; ++$length) {
            $path .= substr($dirname, 0, $length).\DIRECTORY_SEPARATOR;
        }

        return $path.$filename;
    }

    /**
     * Get a path for all files (using glob).
     */
    protected function wildcardPath(): string
    {
        $filename = sprintf($this->format, '*');

        return str_repeat('*'.\DIRECTORY_SEPARATOR, $this->levels).$filename;
    }
}

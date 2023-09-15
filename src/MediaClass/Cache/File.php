<?php
/**
 * CWP Media tool for load flags
 */

// // declare(strict_types=1);

namespace CWP\Cache;

use CWP\Cache\Exception\InvalidArgumentException;
use CWP\Cache\Exception\UnexpectedValueException;
use CWP\Cache\Packer\PackerInterface;
use CWP\Cache\Packer\SerializePacker;

/**
 * Cache file.
 */
class File extends AbstractFile
{
    /**
     * @var string 'embed', 'file', 'mtime'
     */
    protected $ttlStrategy = 'embed';

    /**
     * Create the default packer for this cache implementation.
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new SerializePacker();
    }

    /**
     * Set TTL strategy.
     *
     * @param string $strategy
     */
    protected function setTtlStrategyOption($strategy)
    {
        if (!\in_array($strategy, ['embed', 'file', 'mtime'])) {
            throw new InvalidArgumentException("Unknown strategy '$strategy', should be 'embed', 'file' or 'mtime'");
        }

        $this->ttlStrategy = $strategy;
    }

    /**
     * Get TTL strategy.
     */
    protected function getTtlStrategyOption(): string
    {
        return $this->ttlStrategy;
    }

    /**
     * Get the TTL using one of the strategies.
     *
     * @return int
     */
    protected function getTtl(string $cacheFile)
    {
        switch ($this->ttlStrategy) {
            case 'embed':
                return (int) $this->readLine($cacheFile);
            case 'file':
                return file_exists("$cacheFile.ttl")
                    ? (int) file_get_contents("$cacheFile.ttl")
                    : \PHP_INT_MAX;
            case 'mtime':
                return $this->ttl > 0 ? filemtime($cacheFile) + $this->ttl : \PHP_INT_MAX;
        }

        throw new \InvalidArgumentException("Invalid TTL strategy '{$this->ttlStrategy}'");
    }

    /**
     * Set the TTL using one of the strategies.
     *
     * @param int|null $expiration
     * @param string   $contents
     * @param string   $cacheFile
     *
     * @return string The (modified) contents
     */
    protected function setTtl($expiration, $contents, $cacheFile)
    {
        switch ($this->ttlStrategy) {
            case 'embed':
                $contents = ($expiration ?? \PHP_INT_MAX)."\n".$contents;
                break;
            case 'file':
                if (null !== $expiration) {
                    file_put_contents("$cacheFile.ttl", $expiration);
                }
                break;
            case 'mtime':
                // nothing
                break;
        }

        return $contents;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $cacheFile = $this->getFilename($key);

        if (!file_exists($cacheFile)) {
            return $default;
        }

        if ('embed' === $this->ttlStrategy) {
            [$ttl, $packed] = explode("\n", $this->readFile($cacheFile), 2);
        } else {
            $ttl = $this->getTtl($cacheFile);
        }

        if ((int) $ttl <= time()) {
            $this->deleteFile($cacheFile);

            return $default;
        }

        if (!isset($packed)) {
            $packed = $this->readFile($cacheFile); // Other ttl strategy than embed
        }

        return $this->unpack($packed);
    }

    public function has(string $key): bool
    {
        $cacheFile = $this->getFilename($key);

        if (!file_exists($cacheFile)) {
            return false;
        }

        $ttl = $this->getTtl($cacheFile);

        if ($ttl <= time()) {
            $this->deleteFile($cacheFile);

            return false;
        }

        return true;
    }

    public function set(string $key, mixed $value, int|\DateInterval $ttl = null): bool
    {
        $cacheFile = $this->getFilename($key);
        $packed = $this->pack($value);

        if (!\is_string($packed)) {
            throw new UnexpectedValueException('Packer must create a string for the data to be cached to file');
        }

        $contents = $this->setTtl($this->ttlToTimestamp($ttl), $packed, $cacheFile);

        return $this->writeFile($cacheFile, $contents);
    }
}

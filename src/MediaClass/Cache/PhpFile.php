<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache;

use CWP\Cache\File\BasicFilename;
use CWP\Cache\Packer\PackerInterface;
use CWP\Cache\Packer\SerializePacker;

/**
 * Cache file as PHP script.
 */
class PhpFile extends AbstractFile
{
    /**
     * Create the default packer for this cache implementation.
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new SerializePacker();
    }

    /**
     * Get the filename callable.
     */
    protected function getFilenameOption(): callable
    {
        if (!isset($this->filename)) {
            $this->filename = new BasicFilename('%s.php');
        }

        return $this->filename;
    }

    /**
     * Create a PHP script returning the cached value.
     */
    public function createScript($value, ?int $ttl): string
    {
        $macro = var_export($value, true);

        if (str_contains($macro, 'stdClass::__set_state')) {
            $macro = preg_replace_callback("/('([^'\\\\]++|''\\.)')|stdClass::__set_state/", $macro, function ($match) {
                return empty($match[1]) ? '(object)' : $match[1];
            });
        }

        return null !== $ttl
            ? "<?php return time() < {$ttl} ? {$macro} : false;"
            : "<?php return {$macro};";
    }

    public function get($key, $default = null)
    {
        $cacheFile = $this->getFilename($key);

        if (!file_exists($cacheFile)) {
            return $default;
        }

        $packed = include $cacheFile;

        return false === $packed ? $default : $this->unpack($packed);
    }

    public function has($key)
    {
        return null !== $this->get($key);
    }

    public function set($key, $value, $ttl = null)
    {
        $cacheFile = $this->getFilename($key);

        $packed = $this->pack($value);
        $script = $this->createScript($packed, $this->ttlToTimestamp($ttl));

        return $this->writeFile($cacheFile, $script);
    }
}

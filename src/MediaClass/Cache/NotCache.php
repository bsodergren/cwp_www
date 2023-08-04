<?php
/**
 * CWP Media tool
 */

// // declare(strict_types=1);

namespace CWP\Cache;

use CWP\Cache\Packer\NopPacker;
use CWP\Cache\Packer\PackerInterface;

/**
 * Dummy cache handler.
 */
class NotCache extends AbstractCache
{
    /**
     * Create the default packer for this cache implementation.
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new NopPacker();
    }

    public function delete(string $key): bool
    {
        return true;
    }


    public function get(string $key, mixed $default = null): mixed
    {
        return false;
    }

    public function getMultiple($keys, $default = null): iterable
    {
        return [];
    }

    public function has($key): bool
    {
        return false;
    }

    public function set($key, $value, $ttl = null): bool
    {
        return false;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        return false;
    }

    public function clear(): bool
    {
        return true;
    }
}

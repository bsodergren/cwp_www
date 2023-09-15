<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache;

use CWP\Cache\Packer\NopPacker;
use CWP\Cache\Packer\PackerInterface;

/**
 * Apcu.
 */
class Apcu extends AbstractCache
{
    /**
     * Create the default packer for this cache implementation.
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new NopPacker();
    }

    public function set($key, $value, $ttl = null)
    {
        $ttlSeconds = $this->ttlToSeconds($ttl);

        if (isset($ttlSeconds) && $ttlSeconds <= 0) {
            return $this->delete($key);
        }

        return apcu_store($this->keyToId($key), $this->pack($value), $ttlSeconds ?? 0);
    }

    public function get($key, $default = null)
    {
        $packed = apcu_fetch($this->keyToId($key), $success);

        return $success ? $this->unpack($packed) : $default;
    }

    public function has($key)
    {
        return apcu_exists($this->keyToId($key));
    }

    public function delete($key)
    {
        $id = $this->keyToId($key);

        return apcu_delete($id) || !apcu_exists($id);
    }

    public function clear()
    {
        return apcu_clear_cache();
    }
}

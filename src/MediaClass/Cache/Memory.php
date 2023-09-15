<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache;

use CWP\Cache\Packer\PackerInterface;
use CWP\Cache\Packer\SerializePacker;

/**
 * Memory.
 */
class Memory extends AbstractCache
{
    /**
     * Limit the amount of entries.
     *
     * @var int
     */
    protected $limit = \PHP_INT_MAX;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @var array
     */
    protected $cacheTtl = [];

    /**
     * Create the default packer for this cache implementation.
     * {@internal NopPacker might fail PSR-16, as cached objects would change}.
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new SerializePacker();
    }

    /**
     * Make a clone of this object.
     * Set by cache reference, thus using the same pool.
     *
     * @return static
     */
    protected function cloneSelf(): AbstractCache
    {
        $clone = clone $this;

        $clone->cache = &$this->cache;
        $clone->cacheTtl = &$this->cacheTtl;

        return $clone;
    }

    /**
     * Set the max number of items.
     *
     * @param int $limit
     */
    protected function setLimitOption($limit)
    {
        $this->limit = (int) $limit ?: \PHP_INT_MAX;
    }

    /**
     * Get the max number of items.
     *
     * @return int
     */
    protected function getLimitOption()
    {
        return $this->limit;
    }

    public function get($key, $default = null)
    {
        if (!$this->has($key)) {
            return $default;
        }

        $id = $this->keyToId($key);

        return $this->unpack($this->cache[$id]);
    }

    public function has($key)
    {
        $id = $this->keyToId($key);

        if (!isset($this->cacheTtl[$id])) {
            return false;
        }

        if ($this->cacheTtl[$id] <= time()) {
            unset($this->cache[$id], $this->cacheTtl[$id]);

            return false;
        }

        return true;
    }

    public function set($key, $value, $ttl = null)
    {
        if (\count($this->cache) >= $this->limit) {
            $deleteKey = key($this->cache);
            unset($this->cache[$deleteKey], $this->cacheTtl[$deleteKey]);
        }

        $id = $this->keyToId($key);

        $this->cache[$id] = $this->pack($value);
        $this->cacheTtl[$id] = $this->ttlToTimestamp($ttl) ?? \PHP_INT_MAX;

        return true;
    }

    public function delete($key)
    {
        $id = $this->keyToId($key);
        unset($this->cache[$id], $this->cacheTtl[$id]);

        return true;
    }

    public function clear()
    {
        $this->cache = [];
        $this->cacheTtl = [];

        return true;
    }
}

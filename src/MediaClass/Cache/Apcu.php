<?php

/*
 * This file is part of the Cache package.
 *
 * Copyright (c) Daniel González
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Daniel González <daniel@desarrolla2.com>
 * @author Arnold Daniels <arnold@jasny.net>
 */

// declare(strict_types=1);

namespace CWP\Cache;

use CWP\Cache\Exception\CacheException;
use CWP\Cache\Packer\PackerInterface;
use CWP\Cache\Packer\NopPacker;

/**
 * Apcu
 */
class Apcu extends AbstractCache
{
    /**
     * Create the default packer for this cache implementation
     *
     * @return PackerInterface
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new NopPacker();
    }


    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $ttlSeconds = $this->ttlToSeconds($ttl);

        if (isset($ttlSeconds) && $ttlSeconds <= 0) {
            return $this->delete($key);
        }

        return apcu_store($this->keyToId($key), $this->pack($value), $ttlSeconds ?? 0);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $packed = apcu_fetch($this->keyToId($key), $success);

        return $success ? $this->unpack($packed) : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        return apcu_exists($this->keyToId($key));
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $id = $this->keyToId($key);

        return apcu_delete($id) || !apcu_exists($id);
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return apcu_clear_cache();
    }
}

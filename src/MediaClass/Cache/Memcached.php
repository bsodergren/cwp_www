<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache;

use CWP\Cache\Exception\InvalidArgumentException;
use CWP\Cache\Packer\NopPacker;
use CWP\Cache\Packer\PackerInterface;
use Memcached as MemcachedServer;

/**
 * Memcached.
 */
class Memcached extends AbstractCache
{
    /**
     * @var MemcachedServer
     */
    protected $server;

    public function __construct(MemcachedServer $server)
    {
        $this->server = $server;
    }

    /**
     * Create the default packer for this cache implementation.
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new NopPacker();
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

        if (\strlen($key) > 250) {
            throw new InvalidArgumentException('Key to long, max 250 characters');
        }
    }

    /**
     * Pack all values and turn keys into ids.
     */
    protected function packValues(iterable $values): array
    {
        $packed = [];

        foreach ($values as $key => $value) {
            $this->assertKey(\is_int($key) ? (string) $key : $key);
            $packed[$key] = $this->pack($value);
        }

        return $packed;
    }

    public function get($key, $default = null)
    {
        $this->assertKey($key);

        $data = $this->server->get($key);

        if (MemcachedServer::RES_SUCCESS !== $this->server->getResultCode()) {
            return $default;
        }

        return $this->unpack($data);
    }

    public function has($key)
    {
        $this->assertKey($key);
        $this->server->get($key);

        $result = $this->server->getResultCode();

        return MemcachedServer::RES_SUCCESS === $result;
    }

    public function set($key, $value, $ttl = null)
    {
        $this->assertKey($key);

        $packed = $this->pack($value);
        $ttlTime = $this->ttlToMemcachedTime($ttl);

        if (false === $ttlTime) {
            return $this->delete($key);
        }

        $success = $this->server->set($key, $packed, $ttlTime);

        return $success;
    }

    public function delete($key)
    {
        $this->server->delete($this->keyToId($key));

        $result = $this->server->getResultCode();

        return MemcachedServer::RES_SUCCESS === $result || MemcachedServer::RES_NOTFOUND === $result;
    }

    public function getMultiple($keys, $default = null)
    {
        $this->assertIterable($keys, 'keys not iterable');
        $keysArr = \is_array($keys) ? $keys : iterator_to_array($keys, false);
        array_walk($keysArr, [$this, 'assertKey']);

        $result = $this->server->getMulti($keysArr);

        if (false === $result) {
            return false;
        }

        $items = array_fill_keys($keysArr, $default);

        foreach ($result as $key => $value) {
            $items[$key] = $this->unpack($value);
        }

        return $items;
    }

    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        $packed = $this->packValues($values);
        $ttlTime = $this->ttlToMemcachedTime($ttl);

        if (false === $ttlTime) {
            return $this->server->deleteMulti(array_keys($packed));
        }

        return $this->server->setMulti($packed, $ttlTime);
    }

    public function clear()
    {
        return $this->server->flush();
    }

    /**
     * Convert ttl to timestamp or seconds.
     *
     * @see http://php.net/manual/en/memcached.expiration.php
     *
     * @param int|\DateInterval|null $ttl
     *
     * @return int|null
     *
     * @throws InvalidArgumentException
     */
    protected function ttlToMemcachedTime($ttl)
    {
        $seconds = $this->ttlToSeconds($ttl);

        if ($seconds <= 0) {
            return isset($seconds) ? false : 0;
        }

        /* 2592000 seconds = 30 days */
        return $seconds <= 2592000 ? $seconds : $this->ttlToTimestamp($ttl);
    }
}

<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Cache;

use CWP\Cache\Exception\InvalidArgumentException;
use CWP\Cache\Packer\NopPacker;
use CWP\Cache\Packer\PackerInterface;

/**
 * Use multiple cache adapters.
 */
class Chain extends AbstractCache
{
    /**
     * @var CacheInterface[]
     */
    protected $adapters;

    /**
     * Create the default packer for this cache implementation.
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new NopPacker();
    }

    /**
     * Chain constructor.
     *
     * @param CacheInterface[] $adapters Fastest to slowest
     */
    public function __construct(array $adapters)
    {
        foreach ($adapters as $adapter) {
            if (!$adapter instanceof CacheInterface) {
                throw new InvalidArgumentException('All adapters should be a cache implementation');
            }
        }

        $this->adapters = $adapters;
    }

    public function set($key, $value, $ttl = null)
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->set($key, $value, $ttl) && $success;
        }

        return $success;
    }

    public function setMultiple($values, $ttl = null)
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->setMultiple($values, $ttl) && $success;
        }

        return $success;
    }

    public function get($key, $default = null)
    {
        foreach ($this->adapters as $adapter) {
            $result = $adapter->get($key); // Not using $default as we want to get null if the adapter doesn't have it

            if (isset($result)) {
                return $result;
            }
        }

        return $default;
    }

    public function getMultiple($keys, $default = null)
    {
        $this->assertIterable($keys, 'keys are not iterable');

        $missing = [];
        $values = [];

        foreach ($keys as $key) {
            $this->assertKey($key);

            $missing[] = $key;
            $values[$key] = $default;
        }

        foreach ($this->adapters as $adapter) {
            if (empty($missing)) {
                break;
            }

            $found = [];
            foreach ($adapter->getMultiple($missing) as $key => $value) {
                if (isset($value)) {
                    $found[$key] = $value;
                }
            }

            $values = array_merge($values, $found);
            $missing = array_values(array_diff($missing, array_keys($found)));
        }

        return $values;
    }

    public function has($key)
    {
        foreach ($this->adapters as $adapter) {
            if ($adapter->has($key)) {
                return true;
            }
        }

        return false;
    }

    public function delete($key)
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->delete($key) && $success;
        }

        return $success;
    }

    public function deleteMultiple($keys)
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->deleteMultiple($keys) && $success;
        }

        return $success;
    }

    public function clear()
    {
        $success = true;

        foreach ($this->adapters as $adapter) {
            $success = $adapter->clear() && $success;
        }

        return $success;
    }
}

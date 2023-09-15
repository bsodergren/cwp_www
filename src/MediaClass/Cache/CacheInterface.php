<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Cache;

use CWP\Cache\Packer\PackerInterface;
use Psr\SimpleCache\CacheInterface as PsrCacheInterface;

/**
 * CacheInterface.
 */
interface CacheInterface extends PsrCacheInterface
{
    /**
     * Set option for cache.
     *
     * @return static
     */
    public function withOption(string $key, $value);

    /**
     * Set multiple options for cache.
     *
     * @return static
     */
    public function withOptions(array $options);

    /**
     * Get option for cache.
     *
     * @param string $key
     */
    public function getOption($key);

    /**
     * Set the packer.
     *
     * @return static
     */
    public function withPacker(PackerInterface $packer);
}

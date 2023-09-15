<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Packer;

/**
 * Support packing for Caching adapter.
 */
trait PackingTrait
{
    /**
     * @var PackerInterface
     */
    protected $packer;

    /**
     * Create the default packer for this cache implementation.
     */
    abstract protected static function createDefaultPacker(): PackerInterface;

    /**
     * Set a packer to pack (serialialize) and unpack (unserialize) the data.
     *
     * @return static
     */
    public function withPacker(PackerInterface $packer)
    {
        $cache = $this->cloneSelf();
        $cache->packer = $packer;

        return $cache;
    }

    /**
     * Get the packer.
     */
    protected function getPacker(): PackerInterface
    {
        if (!isset($this->packer)) {
            $this->packer = static::createDefaultPacker();
        }

        return $this->packer;
    }

    /**
     * Pack the value.
     *
     * @return string|mixed
     */
    protected function pack($value)
    {
        return $this->getPacker()->pack($value);
    }

    /**
     * Unpack the data to retrieve the value.
     *
     * @param string|mixed $packed
     *
     * @throws \UnexpectedValueException
     */
    protected function unpack($packed)
    {
        return $this->getPacker()->unpack($packed);
    }
}

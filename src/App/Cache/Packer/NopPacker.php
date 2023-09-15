<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Packer;

/**
 * Don't pack, just straight passthrough.
 */
class NopPacker implements PackerInterface
{
    /**
     * Get cache type (might be used as file extension).
     *
     * @return string
     */
    public function getType()
    {
        return 'data';
    }

    /**
     * Pack the value.
     */
    public function pack($value)
    {
        return $value;
    }

    /**
     * Unpack the value.
     */
    public function unpack($packed)
    {
        return $packed;
    }
}

<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Packer;

/**
 * Interface for packer / unpacker.
 */
interface PackerInterface
{
    /**
     * Get cache type (might be used as file extension).
     *
     * @return string
     */
    public function getType();

    /**
     * Pack the value.
     *
     * @return string|mixed
     */
    public function pack($value);

    /**
     * Unpack the value.
     *
     * @param string|mixed $packed
     *
     * @return string
     *
     * @throws \UnexpectedValueException if the value can't be unpacked
     */
    public function unpack($packed);
}

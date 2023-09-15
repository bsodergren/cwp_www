<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Packer;

use CWP\Cache\Exception\InvalidArgumentException;

/**
 * Pack value through serialization.
 */
class JsonPacker implements PackerInterface
{
    /**
     * Get cache type (might be used as file extension).
     *
     * @return string
     */
    public function getType()
    {
        return 'json';
    }

    /**
     * Pack the value.
     *
     * @return string
     */
    public function pack($value)
    {
        return json_encode($value);
    }

    /**
     * Unpack the value.
     *
     * @param string $packed
     *
     * @throws InvalidArgumentException
     */
    public function unpack($packed)
    {
        if (!\is_string($packed)) {
            throw new InvalidArgumentException('packed value should be a string');
        }

        $ret = json_decode($packed);

        if (!isset($ret) && json_last_error()) {
            throw new \UnexpectedValueException('packed value is not a valid JSON string');
        }

        return $ret;
    }
}

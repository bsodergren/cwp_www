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
class SerializePacker implements PackerInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * SerializePacker constructor.
     *
     * @param array $options Any options to be provided to unserialize()
     */
    public function __construct(array $options = ['allowed_classes' => true])
    {
        $this->options = $options;
    }

    /**
     * Get cache type (might be used as file extension).
     *
     * @return string
     */
    public function getType()
    {
        return 'php.cache';
    }

    /**
     * Pack the value.
     *
     * @return string
     */
    public function pack($value)
    {
        return serialize($value);
    }

    /**
     * Unpack the value.
     *
     * @param string $packed
     *
     * @return string
     *
     * @throws \UnexpectedValueException if he value can't be unpacked
     */
    public function unpack($packed)
    {
        if (!\is_string($packed)) {
            throw new InvalidArgumentException('packed value should be a string');
        }

        return unserialize($packed, $this->options);
    }
}

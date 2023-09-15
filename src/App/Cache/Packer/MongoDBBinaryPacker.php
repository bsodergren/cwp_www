<?php
/**
 * CWP Media tool for load flags
 */

namespace CWP\Cache\Packer;

use MongoDB\BSON\Binary;

/**
 * Pack as BSON binary.
 *
 * @todo Don't use serialize when packer chain is here.
 */
class MongoDBBinaryPacker implements PackerInterface
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
        return 'bson';
    }

    /**
     * Pack the value.
     *
     * @return string
     */
    public function pack($value)
    {
        return new Binary(serialize($value), Binary::TYPE_GENERIC);
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
        if (!$packed instanceof Binary) {
            throw new \InvalidArgumentException('packed value should be BSON binary');
        }

        return unserialize((string) $packed, $this->options);
    }
}

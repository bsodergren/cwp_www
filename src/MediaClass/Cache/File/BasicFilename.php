<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\File;

/**
 * Create a path for a key.
 */
class BasicFilename
{
    /**
     * @var string
     */
    protected $format;

    /**
     * BasicFilename constructor.
     */
    public function __construct(string $format)
    {
        $this->format = $format;
    }

    /**
     * Get the format.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Create the path for a key.
     */
    public function __invoke(string $key): string
    {
        return sprintf($this->format, $key ?: '*');
    }

    /**
     * Cast to string.
     */
    public function __toString(): string
    {
        return $this->getFormat();
    }
}

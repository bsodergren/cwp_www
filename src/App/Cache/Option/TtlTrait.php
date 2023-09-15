<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Option;

use CWP\Cache\Exception\InvalidArgumentException;

/**
 * TTL option.
 */
trait TtlTrait
{
    /**
     * @var int|null
     */
    protected $ttl;

    /**
     * Set the maximum time to live (ttl).
     *
     * @param int|null $value Seconds or null to live forever
     *
     * @throws InvalidArgumentException
     */
    protected function setTtlOption(?int $value): void
    {
        if (isset($value) && $value < 1) {
            throw new InvalidArgumentException('ttl cant be lower than 1');
        }

        $this->ttl = $value;
    }

    /**
     * Get the maximum time to live (ttl).
     */
    protected function getTtlOption(): ?int
    {
        return $this->ttl;
    }
}

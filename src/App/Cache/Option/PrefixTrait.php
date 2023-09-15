<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Option;

/**
 * Prefix option.
 */
trait PrefixTrait
{
    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * Set the key prefix.
     */
    protected function setPrefixOption(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    /**
     * Get the key prefix.
     */
    protected function getPrefixOption(): string
    {
        return $this->prefix;
    }
}

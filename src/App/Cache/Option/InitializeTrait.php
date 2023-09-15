<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Option;

/**
 * Auto initialize the cache.
 */
trait InitializeTrait
{
    /**
     * Is cache initialized.
     *
     * @var bool|null
     */
    protected $initialized = false;

    /**
     * Enable/disable initialization.
     */
    public function setInitializeOption(bool $enabled)
    {
        $this->initialized = $enabled ? (bool) $this->initialized : null;
    }

    /**
     * Should initialize.
     */
    protected function getInitializeOption(): bool
    {
        return null !== $this->initialized;
    }

    /**
     * Mark as initialization required (if enabled).
     */
    protected function requireInitialization()
    {
        $this->initialized = isset($this->initialized) ? false : null;
    }

    /**
     * Initialize.
     */
    abstract protected function initialize(): void;
}

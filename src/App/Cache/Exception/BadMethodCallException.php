<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Exception;

use Psr\SimpleCache\CacheException as PsrCacheException;

/**
 * Exception bad method calls.
 */
class BadMethodCallException extends \BadMethodCallException implements PsrCacheException
{
}

<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Exception;

use Psr\SimpleCache\CacheException as PsrCacheException;

/**
 * Exception for unexpected values when reading from cache.
 */
class UnexpectedValueException extends \UnexpectedValueException implements PsrCacheException
{
}

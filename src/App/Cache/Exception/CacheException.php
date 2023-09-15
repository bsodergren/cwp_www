<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Exception;

use Psr\SimpleCache\CacheException as PsrCacheException;

/**
 * Interface used for all types of exceptions thrown by the implementing library.
 */
class CacheException extends \RuntimeException implements PsrCacheException
{
}

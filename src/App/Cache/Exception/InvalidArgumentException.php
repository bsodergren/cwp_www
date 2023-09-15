<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache\Exception;

use Psr\SimpleCache\InvalidArgumentException as PsrInvalidArgumentException;

/**
 * Exception for invalid cache arguments.
 */
class InvalidArgumentException extends \InvalidArgumentException implements PsrInvalidArgumentException
{
}

<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache;

use CWP\Cache\Exception\UnexpectedValueException;
use CWP\Cache\Packer\PackerInterface;
use CWP\Cache\Packer\SerializePacker;
use Predis\Client;
use Predis\Response\ErrorInterface;
use Predis\Response\ServerException;
use Predis\Response\Status;

/**
 * Predis cache adapter.
 *
 * Errors are silently ignored but ServerExceptions are **not** caught. To PSR-16 compliant disable the `exception`
 * option.
 */
class Predis extends AbstractCache
{
    /**
     * @var Client
     */
    protected $predis;

    /**
     * Class constructor.
     *
     * @see predis documentation about how know your configuration https://github.com/nrk/predis
     */
    public function __construct(Client $client)
    {
        $this->predis = $client;
    }

    /**
     * Create the default packer for this cache implementation.
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new SerializePacker();
    }

    /**
     * Run a predis command.
     *
     * @return mixed|bool
     */
    protected function execCommand(string $cmd, ...$args)
    {
        $command = $this->predis->createCommand($cmd, $args);
        $response = $this->predis->executeCommand($command);

        if ($response instanceof ErrorInterface) {
            return false;
        }

        if ($response instanceof Status) {
            return 'OK' === $response->getPayload();
        }

        return $response;
    }

    /**
     * Set multiple (mset) with expire.
     */
    protected function msetExpire(array $dictionary, ?int $ttlSeconds): bool
    {
        if (empty($dictionary)) {
            return true;
        }

        if (!isset($ttlSeconds)) {
            return $this->execCommand('MSET', $dictionary);
        }

        $transaction = $this->predis->transaction();

        foreach ($dictionary as $key => $value) {
            $transaction->set($key, $value, 'EX', $ttlSeconds);
        }

        try {
            $responses = $transaction->execute();
        } catch (ServerException $e) {
            return false;
        }

        $ok = array_reduce($responses, function ($ok, $response) {
            return $ok && $response instanceof Status && 'OK' === $response->getPayload();
        }, true);

        return $ok;
    }

    public function get($key, $default = null)
    {
        $id = $this->keyToId($key);
        $response = $this->execCommand('GET', $id);

        return !empty($response) ? $this->unpack($response) : $default;
    }

    public function getMultiple($keys, $default = null)
    {
        $idKeyPairs = $this->mapKeysToIds($keys);
        $ids = array_keys($idKeyPairs);

        $response = $this->execCommand('MGET', $ids);

        if (false === $response) {
            return false;
        }

        $items = [];
        $packedItems = array_combine(array_values($idKeyPairs), $response);

        foreach ($packedItems as $key => $packed) {
            $items[$key] = isset($packed) ? $this->unpack($packed) : $default;
        }

        return $items;
    }

    public function has($key)
    {
        return $this->execCommand('EXISTS', $this->keyToId($key));
    }

    public function set($key, $value, $ttl = null)
    {
        $id = $this->keyToId($key);
        $packed = $this->pack($value);

        if (!\is_string($packed)) {
            throw new UnexpectedValueException('Packer must create a string for the data');
        }

        $ttlSeconds = $this->ttlToSeconds($ttl);

        if (isset($ttlSeconds) && $ttlSeconds <= 0) {
            return $this->execCommand('DEL', [$id]);
        }

        return !isset($ttlSeconds)
            ? $this->execCommand('SET', $id, $packed)
            : $this->execCommand('SETEX', $id, $ttlSeconds, $packed);
    }

    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        $dictionary = [];

        foreach ($values as $key => $value) {
            $id = $this->keyToId(\is_int($key) ? (string) $key : $key);
            $packed = $this->pack($value);

            if (!\is_string($packed)) {
                throw new UnexpectedValueException('Packer must create a string for the data');
            }

            $dictionary[$id] = $packed;
        }

        $ttlSeconds = $this->ttlToSeconds($ttl);

        if (isset($ttlSeconds) && $ttlSeconds <= 0) {
            return $this->execCommand('DEL', array_keys($dictionary));
        }

        return $this->msetExpire($dictionary, $ttlSeconds);
    }

    public function delete($key)
    {
        $id = $this->keyToId($key);

        return false !== $this->execCommand('DEL', [$id]);
    }

    public function deleteMultiple($keys)
    {
        $ids = array_keys($this->mapKeysToIds($keys));

        return empty($ids) || false !== $this->execCommand('DEL', $ids);
    }

    public function clear()
    {
        return $this->execCommand('FLUSHDB');
    }
}

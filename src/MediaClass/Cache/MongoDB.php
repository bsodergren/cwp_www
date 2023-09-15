<?php
/**
 * CWP Media tool for load flags
 */

// declare(strict_types=1);

namespace CWP\Cache;

use CWP\Cache\Option\InitializeTrait as InitializeOption;
use CWP\Cache\Packer\MongoDBBinaryPacker;
use CWP\Cache\Packer\PackerInterface;
use MongoDB\BSON\UTCDatetime as BSONUTCDateTime;
use MongoDB\Collection;
use MongoDB\Driver\Exception\RuntimeException as MongoDBRuntimeException;

/**
 * MongoDB cache implementation.
 */
class MongoDB extends AbstractCache
{
    use InitializeOption;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * Class constructor.
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * Initialize the DB collection.
     * Set TTL index.
     */
    protected function initialize(): void
    {
        $this->collection->createIndex(['ttl' => 1], ['expireAfterSeconds' => 0]);
    }

    /**
     * Create the default packer for this cache implementation.
     */
    protected static function createDefaultPacker(): PackerInterface
    {
        return new MongoDBBinaryPacker();
    }

    /**
     * Get filter for key and ttl.
     *
     * @param string|iterable $key
     *
     * @return array
     */
    protected function filter($key)
    {
        if (\is_array($key)) {
            $key = ['$in' => $key];
        }

        return [
            '_id' => $key,
            '$or' => [
                ['ttl' => ['$gt' => new BSONUTCDateTime($this->currentTimestamp() * 1000)]],
                ['ttl' => null],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        $filter = $this->filter($this->keyToId($key));

        try {
            $data = $this->collection->findOne($filter);
        } catch (MongoDBRuntimeException $e) {
            return $default;
        }

        return isset($data) ? $this->unpack($data['value']) : $default;
    }

    public function getMultiple($keys, $default = null)
    {
        $idKeyPairs = $this->mapKeysToIds($keys);

        if (empty($idKeyPairs)) {
            return [];
        }

        $filter = $this->filter(array_keys($idKeyPairs));
        $items = array_fill_keys(array_values($idKeyPairs), $default);

        try {
            $rows = $this->collection->find($filter);
        } catch (MongoDBRuntimeException $e) {
            return $items;
        }

        foreach ($rows as $row) {
            $id = $row['_id'];
            $key = $idKeyPairs[$id];

            $items[$key] = $this->unpack($row['value']);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function has($key)
    {
        $filter = $this->filter($this->keyToId($key));

        try {
            $count = $this->collection->count($filter);
        } catch (MongoDBRuntimeException $e) {
            return false;
        }

        return $count > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $ttl = null)
    {
        $id = $this->keyToId($key);

        $item = [
            '_id' => $id,
            'ttl' => $this->getTtlBSON($ttl),
            'value' => $this->pack($value),
        ];

        try {
            $this->collection->replaceOne(['_id' => $id], $item, ['upsert' => true]);
        } catch (MongoDBRuntimeException $e) {
            return false;
        }

        return true;
    }

    public function setMultiple($values, $ttl = null)
    {
        $this->assertIterable($values, 'values not iterable');

        if (empty($values)) {
            return true;
        }

        $bsonTtl = $this->getTtlBSON($ttl);
        $items = [];

        foreach ($values as $key => $value) {
            $id = $this->keyToId(\is_int($key) ? (string) $key : $key);

            $items[] = [
                'replaceOne' => [
                    ['_id' => $id],
                    [
                        '_id' => $id,
                        'ttl' => $bsonTtl,
                        'value' => $this->pack($value),
                    ],
                    ['upsert' => true],
                ],
            ];
        }

        try {
            $this->collection->bulkWrite($items);
        } catch (MongoDBRuntimeException $e) {
            return false;
        }

        return true;
    }

    public function delete($key)
    {
        $id = $this->keyToId($key);

        try {
            $this->collection->deleteOne(['_id' => $id]);
        } catch (MongoDBRuntimeException $e) {
            return false;
        }

        return true;
    }

    public function deleteMultiple($keys)
    {
        $idKeyPairs = $this->mapKeysToIds($keys);

        try {
            if (!empty($idKeyPairs)) {
                $this->collection->deleteMany(['_id' => ['$in' => array_keys($idKeyPairs)]]);
            }
        } catch (MongoDBRuntimeException $e) {
            return false;
        }

        return true;
    }

    public function clear()
    {
        try {
            $this->collection->drop();
        } catch (MongoDBRuntimeException $e) {
            return false;
        }

        $this->requireInitialization();

        return true;
    }

    /**
     * Get TTL as Date type BSON object.
     *
     * @param int|\DateInterval|null $ttl
     */
    protected function getTtlBSON($ttl): ?BSONUTCDatetime
    {
        return isset($ttl) ? new BSONUTCDateTime($this->ttlToTimestamp($ttl) * 1000) : null;
    }
}

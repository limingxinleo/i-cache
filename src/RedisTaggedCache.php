<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Illuminate\Cache;

use DateInterval;
use DateTimeInterface;

class RedisTaggedCache extends TaggedCache
{
    /**
     * Forever reference key.
     *
     * @var string
     */
    public const REFERENCE_KEY_FOREVER = 'forever_ref';

    /**
     * Standard reference key.
     *
     * @var string
     */
    public const REFERENCE_KEY_STANDARD = 'standard_ref';

    /**
     * Store an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @param null|DateInterval|DateTimeInterface|int $ttl
     * @return bool
     */
    public function put($key, $value, $ttl = null)
    {
        if ($ttl === null) {
            return $this->forever($key, $value);
        }

        $this->pushStandardKeys($this->tags->getNamespace(), $key);

        return parent::put($key, $value, $ttl);
    }

    /**
     * Increment the value of an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @return bool|int
     */
    public function increment($key, $value = 1)
    {
        $this->pushStandardKeys($this->tags->getNamespace(), $key);

        return parent::increment($key, $value);
    }

    /**
     * Decrement the value of an item in the cache.
     *
     * @param string $key
     * @param mixed $value
     * @return bool|int
     */
    public function decrement($key, $value = 1)
    {
        $this->pushStandardKeys($this->tags->getNamespace(), $key);

        return parent::decrement($key, $value);
    }

    /**
     * Store an item in the cache indefinitely.
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function forever($key, $value)
    {
        $this->pushForeverKeys($this->tags->getNamespace(), $key);

        return parent::forever($key, $value);
    }

    /**
     * Remove all items from the cache.
     *
     * @return bool
     */
    public function flush()
    {
        $this->deleteForeverKeys();
        $this->deleteStandardKeys();

        return parent::flush();
    }

    /**
     * Store standard key references into store.
     *
     * @param string $namespace
     * @param string $key
     */
    protected function pushStandardKeys($namespace, $key)
    {
        $this->pushKeys($namespace, $key, self::REFERENCE_KEY_STANDARD);
    }

    /**
     * Store forever key references into store.
     *
     * @param string $namespace
     * @param string $key
     */
    protected function pushForeverKeys($namespace, $key)
    {
        $this->pushKeys($namespace, $key, self::REFERENCE_KEY_FOREVER);
    }

    /**
     * Store a reference to the cache key against the reference key.
     *
     * @param string $namespace
     * @param string $key
     * @param string $reference
     */
    protected function pushKeys($namespace, $key, $reference)
    {
        $fullKey = $this->store->getPrefix() . sha1($namespace) . ':' . $key;

        foreach (explode('|', $namespace) as $segment) {
            $this->store->connection()->sadd($this->referenceKey($segment, $reference), $fullKey);
        }
    }

    /**
     * Delete all of the items that were stored forever.
     */
    protected function deleteForeverKeys()
    {
        $this->deleteKeysByReference(self::REFERENCE_KEY_FOREVER);
    }

    /**
     * Delete all standard items.
     */
    protected function deleteStandardKeys()
    {
        $this->deleteKeysByReference(self::REFERENCE_KEY_STANDARD);
    }

    /**
     * Find and delete all of the items that were stored against a reference.
     *
     * @param string $reference
     */
    protected function deleteKeysByReference($reference)
    {
        foreach (explode('|', $this->tags->getNamespace()) as $segment) {
            $this->deleteValues($segment = $this->referenceKey($segment, $reference));

            $this->store->connection()->del($segment);
        }
    }

    /**
     * Delete item keys that have been stored against a reference.
     *
     * @param string $referenceKey
     */
    protected function deleteValues($referenceKey)
    {
        $values = array_unique($this->store->connection()->smembers($referenceKey));

        if (count($values) > 0) {
            foreach (array_chunk($values, 1000) as $valuesChunk) {
                $this->store->connection()->del(...$valuesChunk);
            }
        }
    }

    /**
     * Get the reference key for the segment.
     *
     * @param string $segment
     * @param string $suffix
     * @return string
     */
    protected function referenceKey($segment, $suffix)
    {
        return $this->store->getPrefix() . $segment . ':' . $suffix;
    }
}

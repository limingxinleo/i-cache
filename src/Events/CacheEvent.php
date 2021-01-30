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
namespace Illuminate\Cache\Events;

abstract class CacheEvent
{
    /**
     * The key of the event.
     *
     * @var string
     */
    public $key;

    /**
     * The tags that were assigned to the key.
     *
     * @var array
     */
    public $tags;

    /**
     * Create a new event instance.
     *
     * @param string $key
     */
    public function __construct($key, array $tags = [])
    {
        $this->key = $key;
        $this->tags = $tags;
    }

    /**
     * Set the tags for the cache event.
     *
     * @param array $tags
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }
}

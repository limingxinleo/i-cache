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

use Illuminate\Cache\Contracts\Store;

class TagSet
{
    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Cache\Contracts\Store
     */
    protected $store;

    /**
     * The tag names.
     *
     * @var array
     */
    protected $names = [];

    /**
     * Create a new TagSet instance.
     */
    public function __construct(Store $store, array $names = [])
    {
        $this->store = $store;
        $this->names = $names;
    }

    /**
     * Reset all tags in the set.
     */
    public function reset()
    {
        array_walk($this->names, [$this, 'resetTag']);
    }

    /**
     * Reset the tag and return the new tag identifier.
     *
     * @param string $name
     * @return string
     */
    public function resetTag($name)
    {
        $this->store->forever($this->tagKey($name), $id = str_replace('.', '', uniqid('', true)));

        return $id;
    }

    /**
     * Get a unique namespace that changes when any of the tags are flushed.
     *
     * @return string
     */
    public function getNamespace()
    {
        return implode('|', $this->tagIds());
    }

    /**
     * Get the unique tag identifier for a given tag.
     *
     * @param string $name
     * @return string
     */
    public function tagId($name)
    {
        return $this->store->get($this->tagKey($name)) ?: $this->resetTag($name);
    }

    /**
     * Get the tag identifier key for a given tag.
     *
     * @param string $name
     * @return string
     */
    public function tagKey($name)
    {
        return 'tag:' . $name . ':key';
    }

    /**
     * Get all of the tag names in the set.
     *
     * @return array
     */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * Get an array of tag identifiers for all of the tags in the set.
     *
     * @return array
     */
    protected function tagIds()
    {
        return array_map([$this, 'tagId'], $this->names);
    }
}

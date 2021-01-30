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

class MemcachedLock extends Lock
{
    /**
     * The Memcached instance.
     *
     * @var \Memcached
     */
    protected $memcached;

    /**
     * Create a new lock instance.
     *
     * @param \Memcached $memcached
     * @param string $name
     * @param int $seconds
     * @param null|string $owner
     */
    public function __construct($memcached, $name, $seconds, $owner = null)
    {
        parent::__construct($name, $seconds, $owner);

        $this->memcached = $memcached;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        return $this->memcached->add(
            $this->name,
            $this->owner,
            $this->seconds
        );
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release()
    {
        if ($this->isOwnedByCurrentProcess()) {
            return $this->memcached->delete($this->name);
        }

        return false;
    }

    /**
     * Releases this lock in disregard of ownership.
     */
    public function forceRelease()
    {
        $this->memcached->delete($this->name);
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return mixed
     */
    protected function getCurrentOwner()
    {
        return $this->memcached->get($this->name);
    }
}

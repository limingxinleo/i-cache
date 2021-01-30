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

class RedisLock extends Lock
{
    /**
     * The Redis factory implementation.
     *
     * @var \Illuminate\Redis\Connections\Connection
     */
    protected $redis;

    /**
     * Create a new lock instance.
     *
     * @param \Illuminate\Redis\Connections\Connection $redis
     * @param string $name
     * @param int $seconds
     * @param null|string $owner
     */
    public function __construct($redis, $name, $seconds, $owner = null)
    {
        parent::__construct($name, $seconds, $owner);

        $this->redis = $redis;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        if ($this->seconds > 0) {
            return $this->redis->set($this->name, $this->owner, 'EX', $this->seconds, 'NX') == true;
        }
        return $this->redis->setnx($this->name, $this->owner) === 1;
    }

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release()
    {
        return (bool) $this->redis->eval(LuaScripts::releaseLock(), 1, $this->name, $this->owner);
    }

    /**
     * Releases this lock in disregard of ownership.
     */
    public function forceRelease()
    {
        $this->redis->del($this->name);
    }

    /**
     * Get the name of the Redis connection being used to manage the lock.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->redis->getName();
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return string
     */
    protected function getCurrentOwner()
    {
        return $this->redis->get($this->name);
    }
}

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

class DynamoDbLock extends Lock
{
    /**
     * The DynamoDB client instance.
     *
     * @var \Illuminate\Cache\DynamoDbStore
     */
    protected $dynamo;

    /**
     * Create a new lock instance.
     *
     * @param \Illuminate\Cache\DynamoDbStore $dynamo
     * @param string $name
     * @param int $seconds
     * @param null|string $owner
     */
    public function __construct(DynamoDbStore $dynamo, $name, $seconds, $owner = null)
    {
        parent::__construct($name, $seconds, $owner);

        $this->dynamo = $dynamo;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    public function acquire()
    {
        return $this->dynamo->add(
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
            return $this->dynamo->forget($this->name);
        }

        return false;
    }

    /**
     * Release this lock in disregard of ownership.
     */
    public function forceRelease()
    {
        $this->dynamo->forget($this->name);
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return mixed
     */
    protected function getCurrentOwner()
    {
        return $this->dynamo->get($this->name);
    }
}

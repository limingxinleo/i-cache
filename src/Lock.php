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

use Hyperf\Support\Traits\InteractsWithTime;
use Hyperf\Utils\Str;
use Illuminate\Cache\Contracts\Lock as LockContract;
use Illuminate\Cache\Contracts\LockTimeoutException;

abstract class Lock implements LockContract
{
    use InteractsWithTime;

    /**
     * The name of the lock.
     *
     * @var string
     */
    protected $name;

    /**
     * The number of seconds the lock should be maintained.
     *
     * @var int
     */
    protected $seconds;

    /**
     * The scope identifier of this lock.
     *
     * @var string
     */
    protected $owner;

    /**
     * The number of milliseconds to wait before re-attempting to acquire a lock while blocking.
     *
     * @var int
     */
    protected $sleepMilliseconds = 250;

    /**
     * Create a new lock instance.
     *
     * @param string $name
     * @param int $seconds
     * @param null|string $owner
     */
    public function __construct($name, $seconds, $owner = null)
    {
        if (is_null($owner)) {
            $owner = Str::random();
        }

        $this->name = $name;
        $this->owner = $owner;
        $this->seconds = $seconds;
    }

    /**
     * Attempt to acquire the lock.
     *
     * @return bool
     */
    abstract public function acquire();

    /**
     * Release the lock.
     *
     * @return bool
     */
    abstract public function release();

    /**
     * Attempt to acquire the lock.
     *
     * @param null|callable $callback
     * @return mixed
     */
    public function get($callback = null)
    {
        $result = $this->acquire();

        if ($result && is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }

        return $result;
    }

    /**
     * Attempt to acquire the lock for the given number of seconds.
     *
     * @param int $seconds
     * @param null|callable $callback
     * @return mixed
     * @throws \Illuminate\Cache\Contracts\LockTimeoutException
     */
    public function block($seconds, $callback = null)
    {
        $starting = $this->currentTime();

        while (! $this->acquire()) {
            usleep($this->sleepMilliseconds * 1000);

            if ($this->currentTime() - $seconds >= $starting) {
                throw new LockTimeoutException();
            }
        }

        if (is_callable($callback)) {
            try {
                return $callback();
            } finally {
                $this->release();
            }
        }

        return true;
    }

    /**
     * Returns the current owner of the lock.
     *
     * @return string
     */
    public function owner()
    {
        return $this->owner;
    }

    /**
     * Specify the number of milliseconds to sleep in between blocked lock acquisition attempts.
     *
     * @param int $milliseconds
     * @return $this
     */
    public function betweenBlockedAttemptsSleepFor($milliseconds)
    {
        $this->sleepMilliseconds = $milliseconds;

        return $this;
    }

    /**
     * Returns the owner value written into the driver for this lock.
     *
     * @return string
     */
    abstract protected function getCurrentOwner();

    /**
     * Determines whether this lock is allowed to release the lock in the driver.
     *
     * @return bool
     */
    protected function isOwnedByCurrentProcess()
    {
        return $this->getCurrentOwner() === $this->owner;
    }
}

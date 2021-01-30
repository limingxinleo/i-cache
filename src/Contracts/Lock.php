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
namespace Illuminate\Cache\Contracts;

interface Lock
{
    /**
     * Attempt to acquire the lock.
     *
     * @param null|callable $callback
     * @return mixed
     */
    public function get($callback = null);

    /**
     * Attempt to acquire the lock for the given number of seconds.
     *
     * @param int $seconds
     * @param null|callable $callback
     * @return mixed
     */
    public function block($seconds, $callback = null);

    /**
     * Release the lock.
     *
     * @return bool
     */
    public function release();

    /**
     * Returns the current owner of the lock.
     *
     * @return string
     */
    public function owner();

    /**
     * Releases this lock in disregard of ownership.
     */
    public function forceRelease();
}

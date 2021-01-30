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
namespace Illuminate\Cache\RateLimiting;

class Unlimited extends GlobalLimit
{
    /**
     * Create a new limit instance.
     */
    public function __construct()
    {
        parent::__construct(PHP_INT_MAX);
    }
}

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

interface Factory
{
    /**
     * Get a cache store instance by name.
     *
     * @param null|string $name
     * @return \Illuminate\Contracts\Cache\Repository
     */
    public function store($name = null);
}

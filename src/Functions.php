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

use Hyperf\Utils\ApplicationContext;
use Illuminate\Cache\Exceptions\InvalidArgumentException;

if (! function_exists('Illuminate\\Cache\\cache')) {
    /**
     * Get / set the specified cache value.
     *
     * If an array is passed, we'll assume you want to put to the cache.
     *
     * @param  dynamic  key|key,default|data,expiration|null
     * @throws \Exception
     * @return \Illuminate\Cache\CacheManager|mixed
     */
    function cache()
    {
        $arguments = func_get_args();
        $manager = ApplicationContext::getContainer()->get(CacheManager::class);

        if (empty($arguments)) {
            return $manager;
        }

        if (is_string($arguments[0])) {
            return $manager->get(...$arguments);
        }

        if (! is_array($arguments[0])) {
            throw new InvalidArgumentException(
                'When setting a value in the cache, you must pass an array of key / value pairs.'
            );
        }

        return $manager->put(key($arguments[0]), reset($arguments[0]), $arguments[1] ?? null);
    }
}

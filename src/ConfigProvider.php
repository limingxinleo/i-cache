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

use Illuminate\Cache\Contracts\Repository;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                Repository::class => CacheManager::class,
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for i-cache.',
                    'source' => __DIR__ . '/../publish/i_cache.php',
                    'destination' => BASE_PATH . '/config/autoload/i_cache.php',
                ],
            ],
        ];
    }
}

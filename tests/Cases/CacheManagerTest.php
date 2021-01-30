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
namespace HyperfTest\Cases;

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\CacheManager;
use Mockery as m;
use Psr\Container\ContainerInterface;

/**
 * @internal
 * @coversNothing
 */
class CacheManagerTest extends AbstractTestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testCustomDriverClosureBoundObjectIsCacheManager()
    {
        $app = m::mock(ContainerInterface::class);
        $app->shouldReceive('get')->with(ConfigInterface::class)->andReturn(new Config([
            'i_cache' => [
                'stores' => [
                    __CLASS__ => [
                        'driver' => __CLASS__,
                    ],
                ],
            ],
        ]));
        $cacheManager = new CacheManager($app);
        $driver = function () {
            return $this;
        };
        $cacheManager->extend(__CLASS__, $driver);
        $this->assertEquals($cacheManager, $cacheManager->store(__CLASS__));
    }

    public function testForgetDriver()
    {
        $cacheManager = m::mock(CacheManager::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $cacheManager->shouldReceive('resolve')
            ->withArgs(['array'])
            ->times(4)
            ->andReturn(new ArrayStore());

        $cacheManager->shouldReceive('getDefaultDriver')
            ->once()
            ->andReturn('array');

        foreach (['array', ['array'], null] as $option) {
            $cacheManager->store('array');
            $cacheManager->store('array');
            $cacheManager->forgetDriver($option);
            $cacheManager->store('array');
            $cacheManager->store('array');
        }

        $this->assertTrue(true);
    }

    public function testForgetDriverForgets()
    {
        $app = m::mock(ContainerInterface::class);
        $app->shouldReceive('get')->with(ConfigInterface::class)->andReturn(new Config([
            'i_cache' => [
                'stores' => [
                    'forget' => [
                        'driver' => 'forget',
                    ],
                ],
            ],
        ]));
        $cacheManager = new CacheManager($app);
        $cacheManager->extend('forget', function () {
            return new ArrayStore();
        });

        $cacheManager->store('forget')->forever('foo', 'bar');
        $this->assertSame('bar', $cacheManager->store('forget')->get('foo'));
        $cacheManager->forgetDriver('forget');
        $this->assertNull($cacheManager->store('forget')->get('foo'));
    }
}

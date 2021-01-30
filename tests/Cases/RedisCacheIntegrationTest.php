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

use HyperfTest\Stub\InteractsWithRedis;
use Illuminate\Cache\RedisStore;
use Illuminate\Cache\Repository;
use Mockery as m;

/**
 * @internal
 * @coversNothing
 */
class RedisCacheIntegrationTest extends AbstractTestCase
{
    use InteractsWithRedis;

    protected function setUp(): void
    {
        $shouldNotSpippend = extension_loaded('swoole')
            || (extension_loaded('swow') && version_compare(PHP_VERSION, '7.3', '>='));
        if (! $shouldNotSpippend) {
            $this->markTestSkipped('hyperf/redis is not avaliable.');

            return;
        }

        parent::setUp();
        $this->setUpRedis();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->tearDownRedis();
        m::close();
    }

    public function testRedisCacheAddTwice()
    {
        $store = new RedisStore($this->factory);
        $repository = new Repository($store);
        $this->assertTrue($repository->add('k', 'v', 3600));
        $this->assertFalse($repository->add('k', 'v', 3600));
        $this->assertGreaterThan(3500, $this->redis->ttl('k'));
    }

    /**
     * Breaking change.
     */
    public function testRedisCacheAddFalse()
    {
        $store = new RedisStore($this->factory);
        $repository = new Repository($store);
        $repository->forever('k', false);
        $this->assertFalse($repository->add('k', 'v', 60));
        $this->assertEquals(-1, $this->redis->ttl('k'));
    }

    /**
     * Breaking change.
     */
    public function testRedisCacheAddNull()
    {
        $store = new RedisStore($this->factory);
        $repository = new Repository($store);
        $repository->forever('k', null);
        $this->assertFalse($repository->add('k', 'v', 60));
    }
}

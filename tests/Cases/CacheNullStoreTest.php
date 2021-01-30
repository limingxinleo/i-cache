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

use Illuminate\Cache\NullStore;

/**
 * @internal
 * @coversNothing
 */
class CacheNullStoreTest extends AbstractTestCase
{
    public function testItemsCanNotBeCached()
    {
        $store = new NullStore();
        $store->put('foo', 'bar', 10);
        $this->assertNull($store->get('foo'));
    }

    public function testGetMultipleReturnsMultipleNulls()
    {
        $store = new NullStore();

        $this->assertEquals([
            'foo' => null,
            'bar' => null,
        ], $store->many([
            'foo',
            'bar',
        ]));
    }

    public function testIncrementAndDecrementReturnFalse()
    {
        $store = new NullStore();
        $this->assertFalse($store->increment('foo'));
        $this->assertFalse($store->decrement('foo'));
    }
}

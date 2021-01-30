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
namespace Illuminate\Cache\Events;

class CacheHit extends CacheEvent
{
    /**
     * The value that was retrieved.
     *
     * @var mixed
     */
    public $value;

    /**
     * Create a new event instance.
     *
     * @param string $key
     * @param mixed $value
     */
    public function __construct($key, $value, array $tags = [])
    {
        parent::__construct($key, $tags);

        $this->value = $value;
    }
}

<?php

namespace Carsguide\Auth\Handlers;

use Psr\SimpleCache\CacheInterface;
use Illuminate\Support\Facades\Cache;

class CacheHandler implements CacheInterface
{
    /**
     * Retrieve decoded JWT from cache
     *
     * @param string $key
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Cache::get($key);
    }

    /**
     * Empty cache for the provided key
     *
     * @param string $key
     * @return void
     */
    public function delete($key)
    {
        Cache::forget($key);
    }

    /**
     * Put decoded JWT in cache
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value, $ttl = null)
    {
        // Cache item for 30 minutes
        Cache::put($key, $value, 30);
    }

    public function clear()
    {
        // TODO: Implement clear() method.
    }

    public function getMultiple($keys, $default = null)
    {
        // TODO: Implement getMultiple() method.
    }

    public function setMultiple($values, $ttl = null)
    {
        // TODO: Implement setMultiple() method.
    }

    public function deleteMultiple($keys)
    {
        // TODO: Implement deleteMultiple() method.
    }

    public function has($key)
    {
        // TODO: Implement has() method.
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Default cache time in seconds (1 hour)
     */
    const DEFAULT_CACHE_TIME = 3600;

    /**
     * Get data from cache or execute callback and store result in cache
     *
     * @param string $key
     * @param int $time
     * @param callable $callback
     * @return mixed
     */
    public function remember(string $key, int $time, callable $callback)
    {
        return Cache::remember($key, $time, $callback);
    }

    /**
     * Get data from cache or execute callback and store result in cache forever
     *
     * @param string $key
     * @param callable $callback
     * @return mixed
     */
    public function rememberForever(string $key, callable $callback)
    {
        return Cache::rememberForever($key, $callback);
    }

    /**
     * Store data in cache
     *
     * @param string $key
     * @param mixed $value
     * @param int $time
     * @return bool
     */
    public function put(string $key, $value, int $time = self::DEFAULT_CACHE_TIME)
    {
        return Cache::put($key, $value, $time);
    }

    /**
     * Store data in cache forever
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function forever(string $key, $value)
    {
        return Cache::forever($key, $value);
    }

    /**
     * Get data from cache
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return Cache::get($key, $default);
    }

    /**
     * Check if cache has key
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return Cache::has($key);
    }

    /**
     * Remove data from cache
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key)
    {
        return Cache::forget($key);
    }

    /**
     * Remove all data from cache
     *
     * @return bool
     */
    public function flush()
    {
        return Cache::flush();
    }

    /**
     * Get cache key for model
     *
     * @param string $model
     * @param int $id
     * @return string
     */
    public function getModelKey(string $model, int $id)
    {
        return "model_{$model}_{$id}";
    }

    /**
     * Get cache key for collection
     *
     * @param string $model
     * @param array $params
     * @return string
     */
    public function getCollectionKey(string $model, array $params = [])
    {
        $paramsString = !empty($params) ? '_' . md5(serialize($params)) : '';
        return "collection_{$model}{$paramsString}";
    }

    /**
     * Get cache key for count
     *
     * @param string $model
     * @param array $params
     * @return string
     */
    public function getCountKey(string $model, array $params = [])
    {
        $paramsString = !empty($params) ? '_' . md5(serialize($params)) : '';
        return "count_{$model}{$paramsString}";
    }

    /**
     * Clear model cache
     *
     * @param string $model
     * @param int $id
     * @return bool
     */
    public function clearModelCache(string $model, int $id)
    {
        return $this->forget($this->getModelKey($model, $id));
    }

    /**
     * Clear collection cache
     *
     * @param string $model
     * @return bool
     */
    public function clearCollectionCache(string $model)
    {
        $keys = Cache::get('collection_keys_' . $model, []);
        
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        
        return Cache::forget('collection_keys_' . $model);
    }
}

<?php

namespace Modules\Reports\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    protected const TTL = 900; // 15 minutes

    public function get(string $key)
    {
        return Cache::get($key);
    }

    public function set(string $key, $value): void
    {
        Cache::put($key, $value, self::TTL);
    }

    public function forget(string $key): void
    {
        Cache::forget($key);
    }
}

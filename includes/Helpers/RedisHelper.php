<?php

namespace PonePaste\Helpers;

use Redis;

class RedisHelper {
    private static $redis = null;

    public static function init() {
        self::$redis = new Redis();
        self::$redis->pconnect(PP_REDIS_HOST);
    }

    public static function redis() {
        return self::$redis;
    }

    public static function setex(string $key, int $ttl, string $value) {
        self::$redis->setex($key, $ttl, $value);
    }

    public static function get(string $key) {
        return self::$redis->get($key);
    }

    public static function exists(string $key) {
        return self::$redis->exists($key);
    }
}

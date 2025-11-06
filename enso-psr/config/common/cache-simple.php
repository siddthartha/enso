<?php

use Psr\SimpleCache\CacheInterface as SimpleCacheInterface;
use Yiisoft\Cache\ArrayCache;

// Create a simple cache adapter for PSR-16 using Yii's cache
return [
    SimpleCacheInterface::class => static function () {
        return new ArrayCache();
    },
];
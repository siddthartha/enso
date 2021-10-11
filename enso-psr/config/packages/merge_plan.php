<?php

declare(strict_types=1);

// Do not edit. Content will be replaced.
return [
    '/' => [
        'common' => [
            '/' => [
                'config/common/*.php',
            ],
            'yiisoft/log-target-file' => [
                'common.php',
            ],
            'yiisoft/log-target-syslog' => [
                'common.php',
            ],
            'yiisoft/aliases' => [
                'common.php',
            ],
            'yiisoft/cache' => [
                'common.php',
            ],
            'yiisoft/profiler' => [
                'common.php',
            ],
        ],
        'events-console' => [
            'yiisoft/log' => [
                'events-console.php',
            ],
        ],
        'events-web' => [
            'yiisoft/log' => [
                'events-web.php',
            ],
            'yiisoft/profiler' => [
                'events-web.php',
            ],
        ],
        'params' => [
            '/' => [
                'config/params.php',
            ],
            'yiisoft/log-target-file' => [
                'params.php',
            ],
            'yiisoft/log-target-syslog' => [
                'params.php',
            ],
            'yiisoft/aliases' => [
                'params.php',
            ],
            'yiisoft/profiler' => [
                'params.php',
            ],
        ],
    ],
];

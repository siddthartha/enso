<?php declare(strict_types = 1);

use Yiisoft\Db\Connection\Dsn;

return [
    'supportEmail' => 'sadovnikoff@gmail.com',
    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__),
            '@assets' => '@public/assets',
            '@assetsUrl' => '@baseUrl/assets',
            '@baseUrl' => '/',
            '@data' => '@root/data',
            '@public' => '@root/public',
            '@resources' => '@root/resources',
            '@runtime' => '@root/runtime',
            '@src' => '@root',
            '@tests' => '@root/Tests',
            '@views' => '@root/views',
            '@vendor' => '@root/vendor',
        ],
    ],
    'yiisoft/db-mysql' => [
        'dsn' => (new Dsn(driver: 'mysql', host: 'db', databaseName: 'enso', port: '3306'))->asString(),
        'username' => 'enso',
        'password' => 'b66772bc'
    ],
];

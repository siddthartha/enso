<?php declare(strict_types = 1);

use Yiisoft\Db\Mysql\Dsn;

return [
    'supportEmail' => 'sadovnikoff@gmail.com',
    'yiisoft/aliases' => [
        'aliases' => [
            '@root' => dirname(__DIR__),
            '@config' => '@root/config',
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
        'dsn' => (new Dsn(
            driver: 'mysql',
            host: getenv('DB_HOST') ?: 'db',
            databaseName: getenv('DB_DATABASE') ?: 'enso',
            port: getenv('DB_LOCAL_PORT') ?: '3306'
        )),
        'username' => getenv('DB_USERNAME') ?: 'enso',
        'password' => getenv('DB_PASSWORD') ?: 'b66772bc'
    ],
];

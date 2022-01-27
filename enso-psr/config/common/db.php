<?php declare(strict_types = 1);

use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Mysql\Connection;

/**
 * config ConnectionInterface::class
 *
 * @var string $params
 */
return [
    ConnectionInterface::class => [
        'class' => Connection::class,
        '__construct()' => [
            'dsn' => $params['yiisoft/db-mysql']['dsn'],
        ],
        'setUsername()' => [$params['yiisoft/db-mysql']['username']],
        'setPassword()' => [$params['yiisoft/db-mysql']['password']],
    ],
];

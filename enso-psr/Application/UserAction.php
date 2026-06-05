<?php

declare(strict_types = 1);

namespace Application;

use Application\Model\User;
use Enso\System\ActionHandler;
use Predis\Client;
use Yiisoft\ActiveRecord\ActiveQuery;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Mysql\Connection;

/**
 * Handles all User model database operations previously in IndexAction.
 */
class UserAction extends ActionHandler
{
    #[Route("/default/user", methods: ["GET"])]
    public function __invoke(): array
    {
        $redis = new Client([
            'scheme' => 'tcp',
            'host'   => 'redis',
            'port'   => 6379,
        ]);

        $redisStatus = $redis->ping('hello');

        /* @var $db Connection */
        $db = $this->_context
            ->getContainer()
            ->get(ConnectionInterface::class);

        /** @TODO: move to migrations */
        $db
            ->createCommand()
            ->dropTable('user')
            ->execute();

        $db
            ->createCommand()
            ->createTable(
                'user',
                [
                    'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                    'username' => 'varchar(50)',
                    'email' => 'varchar(50)',
                    'PRIMARY KEY(id)',
                ],
            )
            ->execute();

        foreach (range(0, 3) as $item)
        {
            $user = $this->_context->getContainer()->get(User::class);
            $user->username = 'user' . rand(0, 1000000);
            $user->email = 'user' . rand(0, 1000000) . '@localhost';
            $user->save();
        }

        $users = (new ActiveQuery($user))
            ->asArray()
            ->all();

        return [
            'context' => [
                'redis' => $redisStatus,
                'database' => ['driver' => $db->getDriverName(), 'version' => $db->getServerInfo(), 'active' => $db->isActive()],
            ],
            'users' => $users ?? [],
        ];
    }
}

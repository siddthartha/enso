<?php
declare(strict_types = 1);
/**
 * Class Application\IndexAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Application\Model\User;
use ArrayIterator;
use Enso\Helpers\Runtime;
use Enso\Helpers\Tree;
use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\ActionHandler;
use Predis\Client;
use Swoole\Coroutine;
use Yiisoft\ActiveRecord\ActiveQuery;
use Yiisoft\ActiveRecord\ActiveRecordFactory;
use Yiisoft\Db\Connection\ConnectionInterface;
use Yiisoft\Db\Mysql\Connection;

/**
 * Description of IndexAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class IndexAction extends ActionHandler
{

    /**
     * @OA\Get(
     *     tags={"default"},
     *     path="/default/index/",
     *     summary="Index",
     *     description="Just an empty default endpoint",
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *    ),
     *    @OA\Response(
     *          response="400",
     *          description="Bad request",
     *          @OA\JsonContent(ref="#/components/schemas/ExceptionResponse")
     *     ),
     * )
     */
    #[Route("/default/index", methods: ["GET"])]
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
            /* @var $user User */
            $user = (
            $this->_context
                ->getContainer()
                ->get(ActiveRecordFactory::class)
            )->createAR(User::class);

            $user->username = 'user' . rand(0, 1000000);
            $user->email = 'user' . rand(0, 1000000) . '@mail.ru';
            $user->save();
        }


        $users = (new ActiveQuery(User::class, $db))
            ->asArray()
            ->all();

        return [
            ...(
                new Tree(
                    $this->_context->getRoutingTree()
                )
            )->next()
        ];


//        return [
//            'context' => [
//                'sapi' => PHP_SAPI,
//                'swoole' => Runtime::haveSwoole() ? [ 'cid' => Coroutine::getCid(), 'pid' => Coroutine::getPcid(Coroutine::getCid()) ] : false,
//                'roadRunner' => Runtime::isGoridge(),
//                'redis' => $redisStatus,
//                'database' => ['driver' => $db->getDriverName(), 'version' => $db->getServerVersion(), 'active' => $db->isActive()],
//            ],
//            'users' => $users ?? [],
//        ];
    }

}
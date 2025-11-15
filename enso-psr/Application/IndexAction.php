<?php
declare(strict_types = 1);
/**
 * Class Application\IndexAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Application\Model\User;
use Application\Service\OpenRouter;
use Enso\Helpers\Runtime;
use Enso\System\ActionHandler;
use Predis\Client;
use Swoole\Coroutine;
use Yiisoft\ActiveRecord\ActiveQuery;
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

        $openRouter = new OpenRouter();

        $response = $openRouter->streamChatCompletions(
            model: 'openai/gpt-oss-20b:free',
            messages: [
                ["role" => "user", "content" => "hello"],
            ]
        );

//        return [
//            ...(
//                new Tree(
//                    $this->_context->getRoutingTree()
//                )
//            )->next()
//        ];

        return [
            'context' => [
                'sapi' => PHP_SAPI,
                'swoole' => Runtime::haveSwoole() ? [ 'cid' => Coroutine::getCid(), 'pid' => Coroutine::getPcid(Coroutine::getCid()) ] : false,
                'roadRunner' => Runtime::isGoridge(),
                'redis' => $redisStatus,
                'response' => $response,
            ],
        ];
    }

}
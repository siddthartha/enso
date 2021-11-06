<?php
declare(strict_types = 1);
/**
 * Class Application\IndexAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Helpers\Runtime;
use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\ActionHandler;
use Predis\Client;
use Swoole\Coroutine;

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

        return [
            'context' => [
                'sapi' => PHP_SAPI,
                'swoole' => Runtime::haveSwoole(),
                'swooleContext' => Runtime::haveSwoole()
                    ? [
                        'cid' => Coroutine::getCid(),
                    ]
                    : false,
                'roadRunner' => Runtime::isGoridge(),
                'redis' => $redisStatus,
            ],
        ];
    }

}
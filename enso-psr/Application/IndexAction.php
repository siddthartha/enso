<?php
declare(strict_types = 1);
/**
 * Class Application\IndexAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\ActionHandler;

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
        return [
            'context' => [
                'sapi' => PHP_SAPI,
                'swoole' => function_exists('swoole_version') ? true : false,
            ],
        ];
    }

}
<?php declare(strict_types = 1);
/**
 * Class Application\SomeAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Helpers\A;
use Enso\Relay\Request;
use HttpSoft\Message\Response;
use Enso\System\ActionHandler;
use GuzzleHttp\Psr7\BufferStream;
use HttpSoft\Message\Stream;
use HttpSoft\Message\StreamFactory;
use OpenApi\Generator;

/**
 * Description of OpenApiAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class OpenApiAction extends ActionHandler
{

    /**
     * @OA\Get(
     *     path="/default/open-api",
     *     @OA\Response(response="200", description="Just some action")
     * )
     */
    #[Route("/default/open-api", methods: ["GET"])]
    public function __invoke(): Response
    {
        $openapi = Generator::scan([
            __DIR__ . '/../public',
            __DIR__ . '/../Enso',
            __DIR__ . '/../Application',
        ]);

        // emit manually
        $body = new BufferStream();
        $body->write($openapi->toJson());

        return (new Response())
            ->withBody($body);
    }
}
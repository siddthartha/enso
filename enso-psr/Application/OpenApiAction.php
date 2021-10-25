<?php
declare(strict_types = 1);
/**
 * Class Application\SomeAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Helpers\A;
use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\ActionHandler;

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
    public function __invoke(): array
    {
        $openapi = \OpenApi\Generator::scan([
            __DIR__ . '/../public',
            __DIR__ . '/../Enso',
            __DIR__ . '/../Application',
        ]);

        return json_decode($openapi->toJson(), true);
        // @TODO: replace with unnecessary
        // de/coding by Response creation
    }
}
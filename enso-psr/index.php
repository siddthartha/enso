<?php
declare(strict_types = 1);
declare(ticks = 1);

require_once './vendor/autoload.php';

use Enso\Enso;
use Enso\Relay\Request;
use Enso\Relay\Response;

/**
 *
 * Sphere test project entry point
 *
 */
$app = new Enso();

$request = new \Enso\System\WebRequest();

$app
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            $request->before = 'GET IN';

            $response = $next->handle($request);

            $response->after = 'GET OUT';

            return $response;
        }
    )
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            $request->system = ['user' => (new \Enso\System\User())->__get_attributes()];

            return $next->handle($request);
        }
    )
    ->addMiddleware(new \Enso\System\Router([
        'some' => [
            'action' => new \Enso\System\Entry(\Enso\System\ActionHandler::class),
        ],
    ]));


$response = $app->run($request);


print ($response);


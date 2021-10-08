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

$request = new Request(['initial request content']);

$app
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            return $next->handle(new Request(['requestBody' => $request->body]));
        }
    )
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            $response = $next->handle(new Request(array_merge($request->body, ['before' => 'second start'])));

            $response->body = array_merge($response->body, ['after' => 'second end']);
            return $response;
        }
    )
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            return $next->handle(new Request([$request->body, ['user' => (string) (new \Enso\System\User())]]));
        }
    )
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            header('Content-type: application/json');
            return new Response($request->body);
        }
    );


$response = $app->run($request);


print ($response);


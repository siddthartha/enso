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

$request = new Request([]);

$app
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            $response = $next->handle(new Request(array_merge($request->body, ['before' => 'GET IN'])));

            $response->body = array_merge($response->body, ['after' => 'GET OUT']);

            return $response;
        }
    )
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            return $next->handle(new Request(array_merge($request->body, ['user' => (new \Enso\System\User())->__get_attributes()])));
        }
    )
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            return new Response($request->body);
        }
    );


$response = $app->run($request);


print ($response);


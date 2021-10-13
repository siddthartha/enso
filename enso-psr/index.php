<?php
declare(strict_types = 1);
declare(ticks = 1);

use Enso\Enso as Application;
use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\Router;
use Enso\System\Target;

require_once './preload.php';

/**
 *
 * Sphere test project entry point
 *
 */
$app = (static fn () => new Application())(); // we should be re-enterable

$request = php_sapi_name() == "cli"
    ? new \Enso\System\CliRequest()
    : \Enso\System\WebRequest::fromGlobals();

$response = $app
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
            $request->before = microtime(true);

            $response = $next->handle($request);

            $response->after = microtime(true);
            return $response;
        }
    )
    ->addMiddleware(new Router([
        'default' => [
            'action' => new Target(\Application\SomeAction::class),
            'index' => new Target(\Application\SomeAnotherAction::class),
        ],
    ]))
    ->run($request);

// $response = $app()
//     ->run($request);

// Emit..

$response->taskDuration = $response->after - $response->before;

print ($response);

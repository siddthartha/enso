<?php
declare(strict_types = 1);
declare(ticks = 1);

use Enso\Enso as Application;
use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\Router;
use Enso\System\Entry;

require_once './preload.php';

/**
 *
 * Sphere test project entry point
 *
 */
$app = new Application();

$request = php_sapi_name() == "cli"
    ? new \Enso\System\CliRequest()
    : new \Enso\System\WebRequest();

$app
    ->addMiddleware(
        function (Request $request, callable $next): Response
        {
//            $request->system = ['user' => (new \Enso\System\User())->attributes];

            $request->before = microtime(true);

            $response = $next->handle($request);

            $response->after = microtime(true);
            return $response;
        }
    )
    ->addMiddleware(new Router([
        'default' => [
            'action' => new Entry(\Application\SomeAction::class),
            'index' => new Entry(\Application\SomeAnotherAction::class),
        ],
    ]));

$response = $app->run($request);

// Emit..
print ($response);

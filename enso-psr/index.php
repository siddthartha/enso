<?php
declare(strict_types = 1);
declare(ticks = 1);

require_once './vendor/autoload.php';

use Enso\Enso as Application;
use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\Router;
use Enso\System\Entry;
use Enso\System\ActionHandler;

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
            $request->system = ['user' => (new \Enso\System\User())->attributes];

            return $next->handle($request);
        }
    )
    ->addMiddleware(new Router([
        'default' => [
            'action' => new Entry(ActionHandler::class),
        ],
    ]));

$response = $app->run($request);

print ($response);

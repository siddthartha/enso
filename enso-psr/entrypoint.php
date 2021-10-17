<?php
declare(strict_types = 1);
declare(ticks = 1);

$started_ts = microtime(true);

use Enso\Enso as Application;
use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\Router;
use Enso\System\Target;
use Enso\System\WebEmitter;

require_once __DIR__ . '/Enso/Helpers/Runtime.php';

if (\Enso\Helpers\Runtime::isSapiAsIsHandled())
{
    return false;
}

require_once __DIR__ . '/preload.php';

\Enso\Helpers\Runtime::supportC3();

$preloaded_ts = microtime(true);

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

$response->preloadDuration = $preloaded_ts - $started_ts;
$response->taskDuration = $response->after - $response->before;

// Emit..
$body = (new \GuzzleHttp\Psr7\BufferStream());
$body->write((string) $response);

(new WebEmitter())->emit($response->withBody($body));

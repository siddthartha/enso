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

$responses = [];

foreach ([1, 2, 3] as $key => $value)
{
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

    $response->preloadDuration = round(round($preloaded_ts - $started_ts, 6) * 1000, 2) . " ms";
    $response->taskDuration = round(round($response->after - $response->before, 6) * 1000, 2) . " ms";

    $responses[] = $response;

    gc_collect_cycles();
}

foreach ($responses as $_response)
{
    // Emit responses..
    $body = (new \GuzzleHttp\Psr7\BufferStream());
    $body->write((string) $_response);

    (new WebEmitter())->emit($_response->withBody($body));
    
    echo "\n";
}

echo "\n";

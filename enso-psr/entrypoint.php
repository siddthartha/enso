<?php
declare(strict_types = 1);
declare(ticks = 1);

$started_ts = microtime(true);

use Enso\Enso as Application;
use Enso\Helpers\Runtime;
use Enso\Relay\
    {MiddlewareInterface, Request, Response};
use Enso\System\
    {CliRequest, WebRequest, WebEmitter, Router, Target};
use GuzzleHttp\Psr7\BufferStream;
use Application\
    {ViewAction, IndexAction, TelegramAction};

require_once __DIR__ . '/Enso/Helpers/Runtime.php';

if (Runtime::isSapiAsIsHandled())
{
    return false;
}

require_once __DIR__ . '/preload.php';

Runtime::supportC3();

$preloaded_ts = microtime(true);

$responses = [];

foreach ([1, 2, 3] as $key => $value)
{
    /**
     *
     * Sphere test project entry point
     *
     */
    $app = (static fn() => new Application()) /* run closure fabric */ (); // we should be re-enterable

    $request = (php_sapi_name() == "cli")
        ? new CliRequest()
        : WebRequest::fromGlobals();

    $response = $app
        ->addMiddleware(
            function (Request $request, callable $next): Response
            {
                $request->before = microtime(true);

                /** @var MiddlewareInterface $next */
                $response = $next->handle($request);

                $response->after = microtime(true);
                return $response;
            }
        )
        ->addMiddleware(
            new Router([
                'default' => [
                    'view' => new Target(ViewAction::class),
                    'index' => new Target(IndexAction::class),
                    'telegram' => new Target(TelegramAction::class),
                ],
            ])
        )
        ->run($request);

    $response->preloadDuration = round(round($preloaded_ts - $started_ts, 6) * 1000, 2) . " ms";
    $response->taskDuration = round(round($response->after - $response->before, 6) * 1000, 2) . " ms";

    $responses[] = $response;

    gc_collect_cycles();
}

foreach ($responses as $_response)
{
    // Emit responses..
    $body = (new BufferStream());
    $body->write((string) $_response);

    (new WebEmitter())
        ->emit(
            $_response->withBody($body)
        );

    echo "\n";
}

echo "\n";

<?php
declare(strict_types = 1);
declare(ticks = 1);

$started_ts = microtime(as_float: true);

use Enso\Enso as Application;
use Enso\Helpers\Runtime;
use Enso\Relay\
    {MiddlewareInterface, Request, Response};
use Enso\System\
    {CliEmitter, CliRequest, WebRequest, WebEmitter, Router, Target};
use GuzzleHttp\Psr7\BufferStream;
use Application\
    {OpenApiAction, ViewAction, IndexAction, TelegramAction};

require_once __DIR__ . '/Enso/Helpers/Runtime.php';

Runtime::supportC3();

if (Runtime::isSapiAsIsHandled())
{
    return false;
}

require_once __DIR__ . '/preload.php';

$preloaded_ts = microtime(as_float: true);

$responses = [];

//foreach ([1, 2, 3] as $key => $value)
//{
    /**
     * Enso application lifecycle entrypoint
     */
    $app = (static fn() => new Application()) /* run closure fabric */ (); // we should be re-enterable

    $request = Runtime::isCLI()
        ? new CliRequest()
        : WebRequest::fromGlobals();

    $response = $app
        ->addLayer(
            /**
             * Add headers
             *
             * @param Request $request
             * @param callable $next
             * @return Response
             */
            middleware: function (Request $request, callable $next): Response
            {
                /** @var MiddlewareInterface $next */
                $response = $next->handle(
                    $request
                );

                return $response
                    ->withHeader('Content-type', 'application/json');
            }
        )
        ->addLayer(
            middleware: new Router([
                'default' => [
                    'view' => new Target(ViewAction::class),
                    'index' => new Target(IndexAction::class),
                    'telegram' => new Target(TelegramAction::class),
                    'open-api' => new Target(OpenApiAction::class),
                ],
            ])
        )
        ->run($request);

    $response->preloadDuration = round(round($preloaded_ts - $started_ts, 6) * 1000, 2) . " ms";
    $response->taskDuration = round(round($response->after - $response->before, 6) * 1000, 2) . " ms";

    // $responses[] = $response;
    $body = (new BufferStream());
    $body->write((string) $response);

    (
    Runtime::isCLI()
        ? new CliEmitter()
        : new WebEmitter()
    )->emit(
            response: $response->withBody($body)
    );

    gc_collect_cycles();
//}

// Emit responses..
//foreach ($responses as &$_response)
//{

    if (Runtime::isCLI())
    {
        echo "\n";
    }
//}

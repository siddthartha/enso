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
use Application\
    {OpenApiAction, ViewAction, IndexAction, TelegramAction};
use Psr\Http\Message\RequestInterface;
use Swoole\Http\Request as SwooleRequest;
use Yiisoft\Di\StateResetter;

require_once __DIR__ . '/Enso/Helpers/Runtime.php';

Runtime::supportC3();

if (Runtime::isSapiAsIsHandled())
{
    return false;
}

require_once __DIR__ . '/preload.php';

$preloaded_ts = microtime(as_float: true);

$applicationRunner = static function ($_injectedRequest = null) use ($started_ts, $preloaded_ts)
{
    /**
     * Enso application lifecycle entrypoint
     */
    $app = (static fn() => new Application()) /* run closure fabric */ (); // we should be re-enterable

    if ($_injectedRequest instanceof SwooleRequest)
    {
        $request = WebRequest::fromSwooleRequest($_injectedRequest);

    }
    elseif ($_injectedRequest instanceof RequestInterface)
    {
        $request = (new WebRequest(data: [], psr: $_injectedRequest));
    }
    else /* if ($_injectedRequest == null) */
    {
        $request = (Runtime::isCLI()
            ? new CliRequest()
            : WebRequest::fromGlobals()
        );
    }


    /** @var Response $response */
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
                    'open-api' => new Target(OpenApiAction::class, ['POST']),
                    'open-api-alias' => 'default/open-api',
                ],
            ])
        )
        ->run($request);

    if (!$response->isError())
    {
        $response->preloadDuration = round(round($preloaded_ts - $started_ts, 6) * 1000, 2) . ' ms';
        $response->taskDuration = round(round($response->after - $response->before, 6) * 1000, 2) . ' ms';
    }

    (Runtime::isCLI()
        ? new CliEmitter() // will not be emitted under swoole coroutine context!
        : new WebEmitter()
    )->emit(
        response: $response
    );

    $app->getContainer()
        ->get(StateResetter::class)
        ->reset();

    gc_collect_cycles();

    return $response; // then should be passed to swoole emitter
};

return $applicationRunner;


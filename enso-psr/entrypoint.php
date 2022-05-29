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
use Psr\Http\Message\
    {RequestInterface, ResponseInterface, ServerRequestInterface};
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

return static function ($_injectedRequest = null) use ($started_ts, $preloaded_ts): ResponseInterface
{

    /**
     * Enso application lifecycle entrypoint
     */
    $app = (static fn(): Application => (new Application( // create application instance ...
        emitter: Runtime::isCLI()
            ? new CliEmitter()
            : new WebEmitter()
    )))
    /* ... AND RUN result of closure fabric immediately -- i mean call created object as callable */
    (); // we should be re-enterable here

    if ($_injectedRequest instanceof SwooleRequest)
    {
        $request = WebRequest::fromSwooleRequest($_injectedRequest);
    }
    elseif ($_injectedRequest instanceof ServerRequestInterface)
    {
        $request = (new WebRequest(psr: $_injectedRequest));
    }
    else /* if ($_injectedRequest == null) */
    {
        $request = (
            Runtime::isCLI()
                ? CliRequest::fromGlobals()
                : WebRequest::fromGlobals()
        );
    }

    $routingTree = $app->getRoutingTree();

    $app
        ->addLayer(
            /**
             * Add headers
             *
             * @param Request $request
             * @param callable $next
             * @return Response
             */
            middleware: function (Request $request, callable $next): ResponseInterface
            {
                /** @var MiddlewareInterface $next */
                $response = $next->handle(
                    $request
                );

                if ($response->hasHeader('Content-type'))
                {
                    return $response;
                }
                return $response
                    ->withHeader('Content-type', 'application/json');
            }
        )
        ->addLayer(
            middleware: new Router($routingTree)
        );

    $response = $app->run($request);

    if ($response instanceof Response && !$response->isError())
    {
        $response->preloadDuration = round(round($preloaded_ts - $started_ts, 6) * 1000, 2) . ' ms';
        $response = $response->collapse(force: true);
    }

    $app
        ->getEmitter()
        ->emit(
            response: $response /*, $request->getOrigin()->getMethod() === Method::HEAD*/
        );

    $app->getContainer()
        ->get(StateResetter::class)
        ->reset();

    gc_collect_cycles();

    return $response; // then should be passed to swoole emitter
};

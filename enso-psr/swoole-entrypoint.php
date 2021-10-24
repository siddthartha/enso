<?php declare(strict_types = 1);

use Enso\Helpers\Runtime;
use Enso\Relay\
    {MiddlewareInterface, Request as EnsoRequest, Response as EnsoResponse};
use GuzzleHttp\Psr7\BufferStream;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

require_once __DIR__ . '/Enso/Helpers/Runtime.php';

if (Runtime::isSapiAsIsHandled())
{
    return false;
}

require_once __DIR__ . '/preload.php';

$httpServer = new Server(host: "0.0.0.0", port: 9999);

$httpServer->set(['log_level' => 2]);

$httpServer->on(
    event_name: "request",
    callback: function (Request $_request, Response $_response)
    {
        $started_ts = $_request->server['request_time'];
        $preloaded_ts = microtime(as_float: true);

        /**
         * Enso application lifecycle entrypoint
         */
        $app = (static fn() => new \Enso\Enso()) /* run closure fabric */ (); // we should be re-enterable

        $request = \Enso\System\WebRequest::fromSwooleRequest($_request);

        $response = $app
            ->addLayer(
                /**
                 * Add headers
                 *
                 * @param EnsoRequest $request
                 * @param callable $next
                 * @return EnsoResponse
                 */
                middleware: function (EnsoRequest $request, callable $next): EnsoResponse
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
                middleware: new \Enso\System\Router([
                    'default' => [
                        'view' => new \Enso\System\Target(\Application\ViewAction::class),
                        'index' => new \Enso\System\Target(\Application\IndexAction::class),
                        'telegram' => new \Enso\System\Target(\Application\TelegramAction::class),
                    ],
                ])
            )
            ->run($request);

        if (!$response->isError())
        {
            $response->preloadDuration = round(round($preloaded_ts - $started_ts, 6) * 1000, 2) . " ms";
            $response->taskDuration = round(round($response->after - $response->before, 6) * 1000, 2) . " ms";

        }

        $body = (new BufferStream());
        $body->write((string) $response);

        $_response->setStatusCode($response->getStatusCode(), $response->getReasonPhrase());
        $_response->header('Content-type', 'application/json');
        $_response->end(content: ($response->isError()
            ? $response->getBody()->getContents()
            : $body->getContents()
        ));

        gc_collect_cycles();
    }
);

$httpServer->start();

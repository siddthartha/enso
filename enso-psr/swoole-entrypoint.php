<?php
declare(strict_types=1);

use Enso\Relay\MiddlewareInterface;
use Enso\Relay\Request as EnsoRequest;
use Enso\Relay\Response as EnsoResponse;
use GuzzleHttp\Psr7\BufferStream;
use Enso\Helpers\Runtime;

require_once __DIR__ . '/Enso/Helpers/Runtime.php';

if (Runtime::isSapiAsIsHandled())
{
    return false;
}

require_once __DIR__ . '/preload.php';

$http = new \Swoole\Http\Server(host: "0.0.0.0", port: 9999);

$http->set(['log_level' => 2]);

$http->on(
    event_name: "request",
    callback: function (\Swoole\Http\Request $_request, \Swoole\Http\Response $_response)
    {
        $started_ts = $preloaded_ts = microtime(as_float: true);

        /**
         * Enso application lifecycle entrypoint
         */
        $app = (static fn() => new \Enso\Enso()) /* run closure fabric */ (); // we should be re-enterable

        $request = (php_sapi_name() == "cli")
            ? new \Enso\System\CliRequest()
            : \Enso\System\WebRequest::fromGlobals();

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

        $response->preloadDuration = round(round($preloaded_ts - $started_ts, 6) * 1000, 2) . " ms";
        $response->taskDuration = round(round($response->after - $response->before, 6) * 1000, 2) . " ms";

        $body = (new BufferStream());
        $body->write((string) $response);

        $_response->header('Content-type', 'application/json');
        $_response->end(content: $body->getContents());

        gc_collect_cycles();
    }
);

$http->start();

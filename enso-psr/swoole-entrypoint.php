<?php declare(strict_types = 1);

use Swoole\Http\
    {Request, Response, Server};
use Enso\Relay\
    {MiddlewareInterface, Request as EnsoRequest, Response as EnsoResponse};

$run = require_once './entrypoint.php';

$httpServer = new Server(host: '0.0.0.0', port: 9999);

$httpServer->set(['log_level' => 2]);

$httpServer->on(
    event_name: 'request',
    callback: function (Request $_request, Response $_response) use (&$run)
    {
        $started_ts = $_request->server['request_time'];
        $preloaded_ts = microtime(as_float: true);

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $run ($_request);

        EnsoResponse::toSwooleResponse($response, $_response);

        gc_collect_cycles();
    }
);

$httpServer->start();

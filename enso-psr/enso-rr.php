<?php declare(strict_types = 1);

use Enso\Relay\Response;
use GuzzleHttp\Psr7\BufferStream;
use HttpSoft\Message\ServerRequestFactory;
use HttpSoft\Message\StreamFactory;
use HttpSoft\Message\UploadedFileFactory;
use Spiral\RoadRunner;

$run = require_once './entrypoint.php';

$serverRequestFactory = new ServerRequestFactory();
$streamFactory = new StreamFactory();
$uploadFactory = new UploadedFileFactory();

$worker = RoadRunner\Worker::create();
$worker = new RoadRunner\Http\PSR7Worker($worker, $serverRequestFactory, $streamFactory, $uploadFactory);

while ($request = $worker->waitRequest())
{
    try
    {
        $response = $run($request);

        if ((int) $response->getBody()->getSize() == 0 && $response instanceof Response)
        {
            // then emit Response data
            $body = (new BufferStream());

            if ($body->write((string) $response))
            {
                $response = $response->withBody($body);
            }
        }

        $worker->respond($response);
    }
    catch (\Throwable $e)
    {
        $worker->getWorker()->error((string) $e);
    }
}
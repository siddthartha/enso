<?php

namespace Enso\System;

use Enso\Helpers\Runtime;
use Enso\Relay\Response;
use GuzzleHttp\Psr7\BufferStream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Yiisoft\Http\Status;

class ExceptionHandler
{
    protected RequestInterface $_request;
    protected mixed $_emitter;

    /**
     * @param RequestInterface $request
     * @param mixed $emitter
     */
    public function __construct(RequestInterface $request, mixed $emitter)
    {
        $this->_request = $request;
        $this->_emitter = $emitter;
    }

    public function handle(\Throwable $throwable): ResponseInterface
    {
        $body = (new BufferStream());
        $body->write(
            json_encode([
                'class' => $throwable::class,
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTrace()
            ])
        );

        /**
         * @OA\Schema(
         *     schema="ExceptionResponse",
         *     required={"class", "message"},
         *     @OA\Property(
         *         property="class",
         *         type="string",
         *     ),
         *     @OA\Property(
         *         property="message",
         *         type="string"
         *     )
         * )
         */
        $response = (new Response())
            ->withStatus(Status::INTERNAL_SERVER_ERROR, 'Internal server error')
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Content-type', 'application/json')
            ->withBody($body);

        if (!Runtime::isDaemon())
        {
            $this->_emitter
                ->emit($response);

            exit (Runtime::EXIT_FATAL);
        }

        return $response;
    }
}
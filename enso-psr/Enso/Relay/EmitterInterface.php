<?php

namespace Enso\Relay;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface EmitterInterface
{
    public function emit(ResponseInterface $response, bool $withoutBody = false, bool $terminateAfter = false): void;
}
<?php

namespace Enso\Relay;

interface ResponseInterface extends MessageInterface
{
    public function getStatus(): mixed;
}
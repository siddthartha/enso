<?php

namespace Enso\Relay;

interface ResponseInterface extends MessageInterface
{
    public function getStatus();
    public function withStatus($status): self;
}
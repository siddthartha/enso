<?php

namespace Enso\Relay;

interface RequestInterface extends MessageInterface
{
    public function getTarget(): mixed;
    public function withTarget($target): self;
}
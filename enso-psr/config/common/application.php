<?php declare(strict_types=1);

use Enso\Helpers\Runtime;
use Enso\Relay\EmitterInterface;
use Enso\Relay\Request;
use Enso\System\CliEmitter;
use Enso\System\CliRequest;
use Enso\System\WebEmitter;
use Enso\System\WebRequest;
use Enso\Relay\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

return [
    EmitterInterface::class => static function (): EmitterInterface
    {
        if (Runtime::isCLI())
        {
            return new CliEmitter();
        }

        return new WebEmitter();
    },
    
    RequestInterface::class => static function (): RequestInterface
    {
        if (Runtime::isCLI())
        {
            return CliRequest::fromGlobals();
        }

        return WebRequest::fromGlobals();
    },
];
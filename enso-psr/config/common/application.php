<?php declare(strict_types=1);

use Enso\Helpers\Runtime;
use Enso\Relay\EmitterInterface;
use Enso\System\CliEmitter;
use Enso\System\WebEmitter;
use Psr\Log\LoggerInterface;

return [
    EmitterInterface::class => static function (): EmitterInterface
    {
        if (Runtime::isCLI())
        {
            return new CliEmitter();
        }

        return new WebEmitter();
    },
];
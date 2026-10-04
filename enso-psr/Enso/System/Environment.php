<?php
declare(strict_types = 1);

namespace Enso\System;

use Enso\Helpers\Runtime;
use Enso\Relay\RequestInterface;

enum Environment: string
{
    case HTTP = 'HTTP';
    case CLI = 'CLI';
    case BOT = 'BOT';
    case MCP = 'MCP';

    public static function fromRuntime(): self
    {
        if (Runtime::isMcp()) {
            return self::MCP;
        }
        if (Runtime::isBot()) {
            return self::BOT;
        }
        if (defined('STDIN') && PHP_SAPI === 'cli') {
            return self::CLI;
        }

        return self::HTTP;
    }
}
<?php

use Enso\Enso;
use Enso\System\Environment;
use Enso\System\Method;
use Enso\System\Target;

/** @var Enso $context */

return [
    'ai' => new Target('Application\LLMStreamAction', [Method::GET, Method::POST], [Environment::HTTP, Environment::CLI]),
    'default' => [
        'index' => new Target('Application\IndexAction', [Method::GET], [Environment::HTTP, Environment::CLI]),
        'user' => new Target('Application\UserAction', [Method::GET], [Environment::HTTP, Environment::CLI]),
        'telegram' => new Target('Application\TelegramAction', [Method::GET], [Environment::HTTP, Environment::CLI]),
        'telegram-send-input' => new Target('Application\TelegramSendInputAction', [], [Environment::MCP]),
        'open-api' => new Target('Application\OpenApiAction', [Method::GET], [Environment::HTTP, Environment::CLI]),
        'open-api-alias' => 'default/open-api',
        'docs' => new Target('Application\DocsAction', [Method::GET], [Environment::HTTP, Environment::CLI]),
        'cv' => new Target('Application\CVAction', [Method::GET], [Environment::HTTP, Environment::CLI]),
        'cv-ru' => new Target('Application\CVRuAction', [Method::GET], [Environment::HTTP, Environment::CLI]),
        'routes' => new Target('Application\RoutesAction', [Method::GET], [Environment::HTTP, Environment::CLI]),
    ],
];

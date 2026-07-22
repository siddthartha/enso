<?php

use Enso\Enso;
use Enso\System\Target;

/** @var Enso $context */

return [
    'ai' => new Target('Application\LLMStreamAction', [], $context),

    'some' => [
        'route1' => 'target1',
        'route2' => 'target2',
    ],
    'default' => [
        'index' => new Target('Application\IndexAction', [], $context),
        'user' => new Target('Application\UserAction', [], $context),
        'telegram' => new Target('Application\TelegramAction'),
        'telegram-send-input' => new Target('Application\TelegramSendInputAction'),
        'open-api' => new Target('Application\OpenApiAction', ['POST']),
        'open-api-alias' => 'default/open-api',
        'docs' => new Target('Application\DocsAction', ['POST']),
        'cv' => new Target('Application\CVAction'),
        'routes' => new Target('Application\RoutesAction', [], $context),
        'test' => ['value' => 123],
    ],
];

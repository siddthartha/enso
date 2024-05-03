<?php

use Enso\Enso;
use Enso\System\Target;

/** @var Enso $context */

return [
    'some' => [
        'route1' => 'target1',
        'route2' => 'target2',
    ],
    'default' => [
        'index' => new Target('Application\IndexAction', [], $context),
        'telegram' => new Target('Application\TelegramAction'),
        'telegram-send-input' => new Target('Application\TelegramSendInputAction'),
        'open-api' => new Target('Application\OpenApiAction', ['POST']),
        'open-api-alias' => 'default/open-api',
        'docs' => new Target('Application\DocsAction', ['POST']),
        'test' => ['value' => 123],
    ],
];

<?php

namespace Application\Service;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class Telegram
{
    public int $telegramBotId;

    public string $telegramBotApiKey;
    public string $telegramApiBaseUrl = 'https://api.telegram.org';

    /**
     * @var Client HTTP Client for making API request
     */
    protected Client $_client;

    public function __construct()
    {
        $this->telegramBotId = getenv('ENSO_TG_BOT_ID');
        $this->telegramBotApiKey = getenv('ENSO_TG_API_KEY');

        $this->_client = new Client([
            'base_uri' => $this->telegramApiBaseUrl,
        ]);

    }

    public function sendMessage(string $message, int $recipientId) : ResponseInterface
    {
        return $this->_client
            ->post(
                $this->getTelegramBotApiUrl() . 'sendMessage',
                [
                    'json' => [
                        'chat_id' => $recipientId,
                        'parse_mode' => 'MarkdownV2',
                        'text' => "```php\n" . $message . "\n```\n",
                    ]
                ]
            );
    }

    public function getTelegramBotApiUrl(): string
    {
        return "{$this->telegramApiBaseUrl}/bot{$this->telegramBotId}:{$this->telegramBotApiKey}/";
    }
}
<?php
declare(strict_types = 1);
/**
 * Class TelegramController
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Helpers\A;
use Enso\System\ActionHandler;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Description of TelegramController
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class TelegramAction extends ActionHandler
{
    //
    public int $telegramBotId = 2010394946;
    public string $telegramBotApiKey = 'AAHecKtpCxZvIIZ-Sgyidoa8YhSPbrXtzUg';
    public string $telegramApiBaseUrl = 'https://api.telegram.org';

    public int $recipientId = 174741219;

    /**
     * @var Client HTTP Client for making API request
     */
    protected Client $_client;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->_client = new Client([
            'base_uri' => $this->telegramApiBaseUrl,
        ]);
    }

    public function getTelegramBotApiUrl()
    {
        return "{$this->telegramApiBaseUrl}/bot{$this->telegramBotId}:{$this->telegramBotApiKey}/";
    }

    /**
     * @return array
     * @throws GuzzleException
     */
    #[Route("/default/telegram", methods: ["POST"])]
    public function __invoke(): array
    {
        $request = $this->getRequest();

        $token = A::getValue($request->getHeaders(), 'X-Gitlab-Token', null);
        $event = A::getValue($request->getHeaders(), 'X-Gitlab-Event', null);

        if ($token != $this->telegramBotApiKey
            || $event != "Push Hook"
        ) {
            throw new \BadMethodCallException();
        }

        $body = json_decode($request->getPSR()->getBody()->getContents());
        $commits = A::getValue($body, 'commits', null);

        if (empty($commits))
        {
            return [];
        }

        foreach ($commits as $commit)
        {
            $_response = $this->_client->post($this->getTelegramBotApiUrl() . 'sendMessage', [
                'json' => [
                    'chat_id' => $this->recipientId,
                    'parse_mode' => 'Markdown',
                    'text' => "`{event template}`"

                ]
            ]);
        }

        return A::merge(
            $this->getRequest()->attributes,
            ['apiResponse' => json_decode($_response->getBody()->getContents(), true)]
        );
    }
}

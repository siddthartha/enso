<?php
declare(strict_types = 1);
/**
 * Class app\modules\api\modules\backend\controllers\TelegramController
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Enso\Helpers\A;
use GuzzleHttp\Client;

/**
 * Description of TelegramController
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class TelegramAction extends \Enso\System\ActionHandler
{
    //
    public $telegramBotId = 2010394946;
    public $telegramBotApiKey = 'AAHecKtpCxZvIIZ-Sgyidoa8YhSPbrXtzUg';
    public $telegramApiBaseUrl = 'https://api.telegram.org';

    public $recipientId = 174741219;

    /**
     *
     * @var Client
     */
    protected $_client;

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

    #[Route("/default/tele", methods: ["GET"])]
    public function __invoke(): array
    {
        $request = $this->getRequest();

        $token = A::getValue($request->getHeaders(), 'X-Gitlab-Token', null);
        $eventName = A::getValue($request->getHeaders(), 'X-Gitlab-Event', null);

        if ($token != $this->telegramBotApiKey
            /* || $event != "Push Hook"*/
        ) {
            //throw new \yii\web\ForbiddenHttpException();
        }

        // $body = json_decode($request->getOrigin()->getBody()->getContents());
//        $commits = A::getValue($body, 'commits', null);
//
//        if (empty($commits))
//        {
//            return false;
//        }

//        foreach ($commits as $commit)
//        {
            $_response = $this->_client->post($this->getTelegramBotApiUrl() . 'sendMessage', [
                'json' => [
                    'chat_id' => $this->recipientId,
                    'parse_mode' => 'Markdown',
                    'text' => "event"

                ]
            ]);
//        }


        return ['before' => $this->getRequest()->before, 'test'];
    }
}

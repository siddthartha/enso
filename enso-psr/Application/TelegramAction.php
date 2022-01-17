<?php
declare(strict_types = 1);
/**
 * Class TelegramController
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Application\Service\Telegram;
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
    public int $recipientId = 174741219;

    private ?Telegram $_telegram;


    public function __construct(?object &$context = null, ?Telegram &$telegram = null)
    {
        parent::__construct($context);

        $this->_telegram = $telegram ?? new Telegram();

    }

    /**
     * @OA\Schema(schema="GitlabEvent", required={"id"})
     */
    /**
     * @OA\Post(
     *     path="/default/telegram",
     *     @OA\Response(response="200", description="An example resource")
     * )
     */
    /**
     * @return array
     * @throws GuzzleException
     */
    #[Route("/default/telegram", methods: ["POST"])]
    public function __invoke(): array
    {
        $request = $this->getRequest();
        $headers = $request->getHeaders();

        $token = A::get($headers, 'X-Gitlab-Token', null);
        $event = A::get($headers, 'X-Gitlab-Event', null);

//        if ($token != $this->telegramBotApiKey
//            || $event != "Push Hook"
//        ) {
//            throw new \BadMethodCallException();
//        }

//        $body = json_decode($request->getPSR()->getBody()->getContents());
//        $commits = A::getValue($body, 'commits', null);
//
//        if (empty($commits))
//        {
//            return [];
//        }
//
//        foreach ($commits as $commit)
//        {
            $_response = $this->_telegram->sendMessage("`{event template}`", $this->recipientId);
//        }

        return A::merge(
            $this->getRequest()->attributes,
            ['apiResponse' => json_decode($_response->getBody()->getContents(), true)]
        );
    }
}

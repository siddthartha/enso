<?php
declare(strict_types = 1);
/**
 * Class Application\SomeAction
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Application;

use Application\Service\Telegram;
use Enso\Enso;
use Enso\Helpers\A;
use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\System\ActionHandler;
use Psr\Http\Message\ResponseInterface;

/**
 * Description of ViewAction
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class TelegramSendInputAction extends ActionHandler
{
    public int $recipientId;

    public function __construct(?Enso &$context = null)
    {
        parent::__construct($context);

        $this->recipientId = (int) getenv('ENSO_TG_RECIPIENT_ID');
    }

    /**
     * @OA\Get(
     *     path="/default/telegram-send-input",
     *     @OA\Response(response="200", description="Just some action")
     * )
     */
    #[Route("/default/telegram-send-input", methods: ["GET"])]
    public function __invoke(): array
    {
        $telegramResponse = (new Telegram())
            ->sendMessage(
                message: $this->getRequest()->getBody()->getContents(),
                recipientId: $this->recipientId
            );

        return A::merge(
            $this->getRequest()->attributes,
            ['apiResponse' => json_decode($telegramResponse->getBody()->getContents(), true)]
        );
    }
}
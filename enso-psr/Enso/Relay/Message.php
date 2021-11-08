<?php
declare(strict_types = 1);
/**
 * Class Enso\Relay\Message
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use HttpSoft\Message\MessageTrait;
use Psr\Http\Message\MessageInterface as PSRMessageInterface;

/**
 * Description of Message
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Message implements MessageInterface, PSRMessageInterface
{
    use MessageTrait;

    private mixed $payload;

    public function getPayload(): mixed
    {
        if ($this->payload === null)
        {
            $this->payload = [];
        }

        return $this->payload;
    }

    public function withPayload($payload): MessageInterface
    {
        $message = clone $this;
        $message->payload = $payload;

        return $message;
    }
}
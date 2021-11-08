<?php
declare(strict_types = 1);
/**
 * Class Enso\Relay\MessageInterface
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

/**
 * Description of MessageInterface
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
interface MessageInterface
{
    public function getPayload(): mixed;
    public function withPayload($payload): self;
}
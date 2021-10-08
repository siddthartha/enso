<?php
declare(strict_types = 1);
/**
 * Class Enso\Relay\Message
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

/**
 * Description of Message
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Message implements MessageInterface
{
    protected $_headers;
    protected $_body;

    public function getHeaders(): array
    {
        return $this->_headers;
    }

    public function getBody(): mixed
    {
        return $this->_body;
    }
}
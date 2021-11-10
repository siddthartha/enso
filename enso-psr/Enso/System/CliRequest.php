<?php
declare(strict_types = 1);
/**
 * Class Enso\System\CliRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Relay\Request;
use Enso\Relay\RequestInterface;

/**
 * Description of CliRequest
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class CliRequest extends Request
{
    private mixed $payload;
    private mixed $target;

    private array $_arguments;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public static function fromGlobals(): self
    {
        $new = new self();

        $new->_arguments = $_SERVER['argv'];
        return $new;
    }

    /**
     *
     * @return array
     */
    public function getRoute(): array
    {
        return isset($this->_arguments[1])
            ? explode('/', $this->_arguments[1])
            : parent::getRoute();
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }

    public function withPayload($payload): RequestInterface
    {
        $new = clone $this;
        $new->payload = $payload;

        return $new;
    }

    public function withTarget($target): RequestInterface
    {
        $new = clone $this;
        $new->target = $target;

        return $new;

    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->_arguments;
    }

    /**
     * @return mixed
     */
    public function getTarget(): mixed
    {
        return $this->target;
    }
}

<?php
declare(strict_types = 1);
/**
 * Class Enso\System\CliRequest
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Relay\Request;
use Enso\Relay\RequestInterface;
use GuzzleHttp\Psr7\CachingStream;
use GuzzleHttp\Psr7\LazyOpenStream;
use Psr\Http\Message\StreamInterface;

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
    private StreamInterface $_body;

    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    public static function fromGlobals(): self
    {
        $new = new self();

        $new->_arguments = $_SERVER['argv'];
        $new->_body = new CachingStream(new LazyOpenStream('php://stdin', 'r+'));

        return $new;
    }

    /**
     *
     * @return array
     */
    public function getRoute(): array
    {
        if (!isset($this->_arguments[1])) {
            return parent::getRoute();
        }
        
        $path = $this->_arguments[1];
        $route = $this->preparePath($path);
        
        return empty($route) ? parent::getRoute() : $route;
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

    public function  hasArguments(): bool
    {
        return !empty($this->_arguments) && count($this->_arguments) > 2;
    }

    public function getArgumentsLine(): string
    {
        return implode(" ", array_slice($this->_arguments, 2));
    }

    /**
     * @return mixed
     */
    public function getTarget(): mixed
    {
        return $this->target;
    }

    public function getBody(): StreamInterface
    {
        return $this->_body;
    }
}

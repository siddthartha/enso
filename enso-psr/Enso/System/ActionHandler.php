<?php
declare(strict_types = 1);
/**
 * Class Enso\System\ActionInterface
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Enso;
use Psr\Http\Message\ResponseInterface;
use Enso\Relay\
    {Response, MiddlewareInterface};
use Psr\Http\Message\RequestInterface;

/**
 * Description of ActionInterface
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class ActionHandler implements MiddlewareInterface
{
    protected ?Enso $_context;
    protected RequestInterface $_request;

    /**
     * @param Enso|null $context
     */
    public function __construct(?Enso &$context = null)
    {
        $this->_context = &$context;

        $this->init();
    }

    public function init()
    {
        ;
    }

    /**
     *
     * @param RequestInterface $request
     * @param callable|null $next
     * @return Response
     */
    public function handle(RequestInterface $request, callable $next = null): ResponseInterface
    {
        $this->_request = $request;

        $result = ($this) (); // __invoke current ansector's object to -- "run" action

        return $result instanceof ResponseInterface
            ? $result
            : new Response($result);
    }

    public function getRequest(): RequestInterface
    {
        return $this->_request;
    }

    /**
     * Single Action use
     */
    public function __invoke()
    {
        return null;
    }
}
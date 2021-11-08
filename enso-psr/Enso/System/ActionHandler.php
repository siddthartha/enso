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
    {Request, Response, MiddlewareInterface};
use Psr\Http\Message\RequestInterface;

/**
 * Description of ActionInterface
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class ActionHandler implements MiddlewareInterface
{
    protected object $_context;
    protected Request $_request;

    /**
     * @param object|null $context
     */
    public function __construct(?object &$context = null)
    {
        if ($context instanceof Enso)
        {
            $this->_context = $context;
        }
    }

    /**
     *
     * @param Request $request
     * @param callable|null $next
     * @return Response
     */
    public function handle(Request $request, callable $next = null): ResponseInterface
    {
        $this->_request = $request;

        $result = ($this) (); // invoke this object to "run" action

        return $result instanceof ResponseInterface
            ? $result
            : new Response($result);
    }

    public function getRequest(): RequestInterface
    {
        return $this->_request;
    }

    /**
     *
     */
    public function __invoke()
    {
        return null;
    }
}
<?php
declare(strict_types = 1);
/**
 * Class Enso\System\Router
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Relay\
    {MiddlewareInterface, Request, Response};
use Enso\Helpers\A;

/**
 * Description of Router
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Router implements MiddlewareInterface
{
    protected $_routes;

    public function __construct(array $routes = [])
    {
        $this->_routes = $routes;
    }

    /**
     *
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function handle(Request $request, mixed $next = null): Response
    {
        $entry = false;

        foreach ($request->getRoute() as $path)
        {
            $entry = A::get($this->_routes, $path, null);
            $this->_routes = A::get($this->_routes, $path, null);

            if (!$this->_routes)
            {
                throw new \BadMethodCallException("No route found.");
            }
        }

        if ($entry instanceof Target)
        {
            $action = $entry->getInstance();

            return $action->handle($request);
        }

        throw new \BadMethodCallException("No route found.");
    }
}
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
    protected array $_routes;

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
        $routesList = $this->_routes;

        foreach ($request->getRoute() as $pathEntry)
        {
            $entry = A::get($routesList, $pathEntry, null);
            $routesList = A::get($routesList, $pathEntry, null);

            if (!$routesList)
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
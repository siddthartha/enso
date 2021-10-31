<?php
declare(strict_types = 1);
/**
 * Class Enso\System\Router
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Relay\
{MiddlewareInterface, Request, Response
};
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
        $targetRoute = $request->getRoute();
        $routesTree = $this->getRoutes();

        $action = $this->resolve($targetRoute, $routesTree);

        return $action->handle($request);
    }

    protected function resolve(array $path, array $routesTree): ActionHandler
    {
        reset($path);

        while ($pathEntry = current($path))
        {
            $entry = A::get($routesTree, $pathEntry, null);
            $routesTree = A::get($routesTree, $pathEntry, null);

            if (!$routesTree)
            {
                throw new \BadMethodCallException("No route found.");
            }

            if ($entry instanceof Target)
            {
                $action = $entry->getInstance();

                return $action;
            }

            if (is_string($entry))
            {
                $routesTree = $this->getRoutes();
                $path = explode('/', trim($entry, '\n\r\t\0\x0B /'));
                reset($path);

                return $this->resolve($path, $routesTree);
            };

            next($path);
        }

        throw new \BadMethodCallException("No route found.");
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->_routes;
    }
}
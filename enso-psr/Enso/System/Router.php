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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Description of Router
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Router implements MiddlewareInterface
{
    public const NO_ROUTE_FOUND_MESSAGE = 'No route found';
    public const ROUTE_TOKEN_DELIMITER = '/';
    public const ROUTE_TRIM_PREFIX = '\n\r\t\0\x0B ';

    protected array $_routes;

    public function __construct(array $routes = [])
    {
        $this->_routes = $routes;
    }

    /**
     *
     * @param RequestInterface $request
     * @param callable $next
     * @return Response
     */
    public function handle(RequestInterface $request, mixed $next = null): ResponseInterface
    {
        /** @TODO: make transparent for non-enso request */
        /** @var $targetRoute */
        $targetRoute = $request->getRoute();
        $routesTree = $this->getRoutes();

        $action = $this->resolve($targetRoute, $routesTree);

        return $action->handle($request);
    }

    /**
     * @param array $path
     * @param array $routesTree
     * @return ActionHandler
     */
    protected function resolve(array $path, array $routesTree): ActionHandler
    {
        reset($path);

        while ($pathEntry = current($path))
        {
            $entry = A::get($routesTree, $pathEntry, null);
            $routesTree = A::get($routesTree, $pathEntry, null);

            if (!$routesTree)
            {
                throw new \BadMethodCallException(self::NO_ROUTE_FOUND_MESSAGE);
            }

            if ($entry instanceof Target)
            {
                return $entry->getInstance();
            }

            if (is_string($entry))
            {
                $routesTree = $this->getRoutes();
                $path = explode(
                    self::ROUTE_TOKEN_DELIMITER,
                    trim($entry, self::ROUTE_TRIM_PREFIX . self::ROUTE_TOKEN_DELIMITER)
                );

                reset($path);

                /**
                 * RECURSIVE CALL for elements with redirection string
                 */
                return $this->resolve($path, $routesTree);
            };

            next($path);
        }

        throw new \BadMethodCallException(self::NO_ROUTE_FOUND_MESSAGE);
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->_routes;
    }
}
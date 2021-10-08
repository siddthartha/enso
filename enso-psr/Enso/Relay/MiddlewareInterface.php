<?php declare(strict_types = 1);
/**
 * Class Enso\Relay\Middleware
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

/**
 * Description of Middleware
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next = null): Response;
}
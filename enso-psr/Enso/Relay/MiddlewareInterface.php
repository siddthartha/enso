<?php declare(strict_types = 1);
/**
 * Class Enso\Relay\Middleware
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Psr\Http\Message\RequestInterface as PSRRequestInterface;
use Psr\Http\Message\ResponseInterface as PSRResponseInterface;

/**
 * Description of Middleware
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
interface MiddlewareInterface
{
    public function handle(PSRRequestInterface $request, ?callable $next = null): PSRResponseInterface;
}
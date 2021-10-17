<?php declare(strict_types = 1);
/**
 * Class Enso\Relay
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


/**
 * Description of Relay
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Relay extends RequestHandler
{

    public function add(mixed $middleware): void
    {
        $this->_queue[] = $middleware;
    }

    public function handle(RequestInterface $request, callable $next = null): ResponseInterface
    {
        reset($this->_queue);

        $runner = new Runner($this->_queue);

        return $runner->handle($request);
    }
}
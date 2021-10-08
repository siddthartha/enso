<?php declare(strict_types = 1);
/**
 * Class Enso\Relay
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

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

    public function handle(Request $request, callable $next = null): Response
    {
        reset($this->_queue);

        $runner = new Runner($this->_queue);

        return $runner->handle($request);
    }
}
<?php
declare(strict_types = 1);
/**
 * Class Enso\System\ActionInterface
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

use Enso\Relay\Request;
use Enso\Relay\Response;
use Enso\Relay\MiddlewareInterface;

/**
 * Description of ActionInterface
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class ActionHandler implements MiddlewareInterface
{
    protected $_request;

    /**
     *
     * @param \Enso\Relay\Request $request
     * @param callable $next
     * @return \Enso\Relay\Response
     */
    public function handle(Request $request, callable $next = null): Response
    {
        $this-> _request = $request;

        return new Response(($this)());
    }

    /**
     *
     */
    public function __invoke()
    {
        return null;
    }
}
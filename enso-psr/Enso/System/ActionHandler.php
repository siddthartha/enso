<?php
declare(strict_types = 1);
/**
 * Class Enso\System\ActionInterface
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\System;

/**
 * Description of ActionInterface
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class ActionHandler implements \Enso\Relay\MiddlewareInterface
{
    /**
     *
     * @param \Enso\Relay\Request $request
     * @param \Enso\System\callable $next
     * @return \Enso\Relay\Response
     */
    public function handle(\Enso\Relay\Request $request, ?callable $next = null): \Enso\Relay\Response
    {
        return new \Enso\Relay\Response($request->attributes);
    }
}
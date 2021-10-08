<?php declare(strict_types=1);

namespace Enso;

use Enso\Relay\Relay;
use Enso\Relay\Response;
use Enso\Relay\Request;

/**
 * Класс Enso
 *
 * @author Anton Sadovnikov <sadovnikoff@gmail.com>
 */
class Enso
{

    /**
     * Use Enso behavior traits
     */
    use \Enso\Single;   // singleton

    use \Enso\Subject;  // properties

    private $_relay;

    /**
     * Singleton magic constructor
     * runs only once if no copy of object found
     */
    public function __init(): void
    {
        $this->_relay = new Relay([
            /**
             * First element of Application middlewares queue
             */
            function (Request $request, callable $next): Response
            {
                header('Content-type: application/json');

                return $next->handle(
                    $request
                );
            }
        ]);

    }

    /**
     *
     * @return \Enso\System\User
     */
    public function __get_systemUser(): System\User
    {
        return new System\User();
    }

    /**
     *
     * @param \Enso\callable $middleware
     * @return \self
     */
    public function addMiddleware(callable $middleware): self
    {
        $this->_relay->add($middleware);

        return $this;
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function run(Request $request = null): Response
    {
        return $this->_relay->handle($request);
    }
}

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
        $this->arguments = $_SERVER['argv'];

        $this->_relay = new Relay([
            function (Request $request, callable $next): Response
            {
                return $next->handle(new Request([$request->body, 'post' => $_POST]));
            }
        ]);

    }

    public function __get_systemUser(): System\User
    {
        return new System\User();
    }

    public function addMiddleware(callable $middleware): self
    {
        $this->_relay->add($middleware);

        return $this;
    }

    public function run(Request $request = null): Response
    {
        return $this->_relay->handle($request);
    }
}

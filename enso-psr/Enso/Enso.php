<?php declare(strict_types=1);

namespace Enso;

use Enso\Relay\Relay;
use Enso\Relay\Response;
use Enso\Relay\Request;
use Enso\System\WebEmitter;

use Psr\Log\LoggerInterface;
use Yiisoft\Config\Config;
use Yiisoft\Di\Container;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;

use function dirname;

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
//    use \Enso\Single;   // singleton

    use \Enso\Subject;  // properties

    private $_config;

    private $_container;

    private $_logger;

    private $_relay;

    /**
     * Singleton magic constructor
     * runs only once if no copy of object found
     */
    public function __construct()
    {
        $this->_config = new Config(
            dirname(__DIR__),
            '/config/packages', // Configs path.
            null,
            [
                'params',
                'events',
                'events-web',
                'events-console',
            ]
        );

        $this->_container = new Container(
            $this->_config->get('common')
        );

        $this->_logger = $this->_container->get(LoggerInterface::class);

        $this->_relay = new Relay([
            /**
             * First element of Application middlewares queue
             */
            function (Request $request, callable $next): Response
            {
                header_remove();
                header('Content-type: application/json; charset=utf-8');

                return $next->handle(
                    $request
                );
            }
        ]);

    }

    /**
     *
     * @param mixed $middleware
     * @return \self
     */
    public function addMiddleware(mixed $middleware): self
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
        try
        {
            return $this->_relay->handle($request);
        }
        catch (\Throwable $exc)
        {
            $body = (new \GuzzleHttp\Psr7\BufferStream());
            $body->write(
                json_encode([
                    'class' => $exc::class,
                    'message' => $exc->getMessage(),
                    'file' => $exc->getFile(),
                    'line' => $exc->getLine(),
                    'trace' => $exc->getTrace()
                ])
            );

            $response = (new \GuzzleHttp\Psr7\Response())
                ->withStatus(Status::INTERNAL_SERVER_ERROR, $exc->getMessage())
                ->withHeader('Content-type', 'application/json')
                ->withBody($body);

            (new WebEmitter())->emit($response, $request->getOrigin()->getMethod() === Method::HEAD);

            exit(1);
        }
        finally
        {
        }
    }
}

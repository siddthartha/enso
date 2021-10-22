<?php declare(strict_types=1);

namespace Enso;

use Enso\Relay\
    {MiddlewareInterface, Relay, Response, Request};
use Enso\System\WebEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use GuzzleHttp\Psr7\BufferStream;
use Yiisoft\
    {Config\Config, Di\Container, Http\Method, Http\Status};

use function dirname;

/**
 * Класс Enso
 *
 * @author Anton Sadovnikov <sadovnikoff@gmail.com>
 */
class Enso
{
    use Subject;  // attach "magic" properties getter/setter

    private Config $_config;

    private Container $_container;

    private LoggerInterface $_logger;

    private Relay $_relay;

    /**
     * Singleton magic constructor
     * runs only once if no copy of object found
     *
     * @throws \Yiisoft\Definitions\Exception\InvalidConfigException
     * @throws \ErrorException
     */
    public function __construct()
    {
        $this->_config = new Config(
            dirname(__DIR__),
            configsPath: '/config/packages', // Configs path.
            environment: null,
            recursiveMergeGroups: [
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
             * First Application middleware layer in queue
             */
            function (Request $request, callable $next): Response
            {
                /** @var MiddlewareInterface $next */
                $response = $next->handle(
                    $request
                );

                return $response
                    ->withHeader('Content-type', 'application/json');
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
     * @param Request|null $request
     * @return Response
     */
    public function run(Request $request = null): ResponseInterface
    {
        try
        {
            return $this->_relay->handle($request);
        }
        catch (\Throwable $exc)
        {
            $body = (new BufferStream());
            $body->write(
                json_encode([
                    'class' => $exc::class,
                    'message' => $exc->getMessage(),
                    'file' => $exc->getFile(),
                    'line' => $exc->getLine(),
                    'trace' => $exc->getTrace()
                ])
            );

            $response = (new Response())
                ->withStatus(Status::INTERNAL_SERVER_ERROR, $exc->getMessage())
                ->withHeader('Content-type', 'application/json')
                ->withBody($body);

            (new WebEmitter())->emit($response/*, $request->getOrigin()->getMethod() === Method::HEAD*/);

            exit(-1);
        }
        finally
        {
        }
    }
}

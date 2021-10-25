<?php declare(strict_types = 1);

namespace Enso;

use Enso\Helpers\Runtime;
use Enso\System\CliEmitter;
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
     *
     * @throws \Yiisoft\Definitions\Exception\InvalidConfigException
     * @throws \ErrorException
     */
    public function __construct()
    {
        $this->_config = new Config(
            dirname(__DIR__),
            configsPath: '/config/packages',
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
            function (Request $request, callable $next): Response
            {
                $request->before = microtime(true);

                /** @var MiddlewareInterface $next */
                $response = $next->handle($request);

                $response->before = $request->before;
                $response->after = microtime(true);
                return $response;
            },
        ]);

    }

    /**
     *
     * @param mixed $middleware
     * @return \self
     */
    public function addLayer(mixed $middleware): self
    {
        $this
            ->getRelay()
            ->add($middleware);

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
            return $this
                ->getRelay()
                ->handle($request)
                ->withHeader('Access-Control-Allow-Origin', '*');
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

            /**
             * @OA\Schema(
             *     schema="ExceptionResponse",
             *     required={"class", "message"},
             *     @OA\Property(
             *         property="class",
             *         type="string",
             *     ),
             *     @OA\Property(
             *         property="message",
             *         type="string"
             *     )
             * )
             */
            $response = (new Response())
                ->withStatus(Status::INTERNAL_SERVER_ERROR, $exc->getMessage())
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Content-type', 'application/json')
                ->withBody($body);

            if (!Runtime::isInSwoole())
            {
                $this->getEmitter()->emit($response/*, $request->getOrigin()->getMethod() === Method::HEAD*/);

                exit(-1);
            }

            return $response;
        }
    }

    public function getEmitter()
    {
        if(Runtime::isCLI())
        {
            return new CliEmitter();
        }
        else
        {
            return new WebEmitter();
        }
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->_container;
    }

    /**
     * @return Relay
     */
    public function getRelay(): Relay
    {
        return $this->_relay;
    }

    /**
     * @return array|mixed|object|LoggerInterface
     */
    public function getLogger(): mixed
    {
        return $this->_logger;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->_config;
    }
}

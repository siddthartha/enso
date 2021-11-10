<?php declare(strict_types = 1);

namespace Enso;

use Enso\System\CliEmitter;
use Enso\System\ExceptionHandler;
use Enso\Relay\
    {EmitterInterface, MiddlewareInterface, Relay, Response, Request};
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Definitions\Exception\
    {CircularReferenceException, InvalidConfigException, NotFoundException, NotInstantiableException};
use Yiisoft\
    {Cache\CacheInterface,
    Config\Config,
    Config\ConfigPaths,
    Di\Container};

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

    private ContainerInterface $_container;

    private LoggerInterface $_logger;

    private Relay $_relay;

    private CacheInterface $_cache;

    private EmitterInterface $_emitter;

    /**
     *
     * @param ContainerInterface|null $container
     * @param Config|null $config
     * @param Relay|null $relay
     * @param CacheInterface|null $cache
     * @param LoggerInterface|null $logger
     * @param EmitterInterface|null $emitter
     * @throws \ErrorException
     * @throws CircularReferenceException
     * @throws InvalidConfigException
     * @throws NotFoundException
     * @throws NotInstantiableException
     */
    public function __construct(
        ?ContainerInterface $container = null,
        ?Config $config = null,
        ?Relay $relay = null,
        ?CacheInterface $cache = null,
        ?LoggerInterface $logger = null,
        ?EmitterInterface $emitter = null,
    ) {

        $this->_config = $config ?? new Config(
            paths: new ConfigPaths(
                rootPath: dirname(__DIR__),
                configDirectory: './config',
                vendorDirectory: './vendor',
            ),
            environment: null,
            paramsGroup: 'params',
        );

        $this->_container = $container ?? new Container(
            $this->_config->get('common')
        );

        $this->_logger = $logger ?? $this->_container->get(LoggerInterface::class);

        $this->_cache = $cache ?? $this->_container->get(CacheInterface::class);

        $this->_relay = $relay ?? new Relay([
            function (Request $request, callable $next): ResponseInterface
            {
                $request->before = microtime(true);

                /** @var MiddlewareInterface $next */
                $response = $next->handle($request);

                if ($response instanceof Response && !$response->isError())
                {
                    $response->before = $request->before;
                    $response->after = microtime(true);
                    $response->taskDuration = round(round($response->after - $response->before, 6) * 1000, 2) . ' ms';
                }

                return $response;
            },
        ]);

        $this->_emitter = $emitter ?? new CliEmitter();
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
     * @return ResponseInterface
     */
    public function run(?Request $request = null): ResponseInterface
    {
        try
        {
            $response = $this->getRelay()
                ->handle($request);

            if ($response instanceof Response)
            {
                $response = $response->collapse();
            }

            return $response
                ->withHeader('Access-Control-Allow-Origin', '*');
        }
        catch (\Throwable $exception)
        {
            return (new ExceptionHandler($request, $this->getEmitter()))
                ->handle($exception);
        }
    }

    public function getEmitter(): EmitterInterface
    {
        return $this->_emitter;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
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

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface
    {
        return $this->_cache;
    }
}

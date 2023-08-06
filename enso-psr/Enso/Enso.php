<?php declare(strict_types = 1);

namespace Enso;

use Enso\System\CliEmitter;
use Enso\System\ExceptionHandler;
use Enso\Relay\
    {EmitterInterface, MiddlewareInterface, Relay, Response, Request};
use ErrorException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Definitions\Exception\
    {CircularReferenceException, InvalidConfigException, NotInstantiableException};
use Yiisoft\Aliases\Aliases;
use Yiisoft\Cache\CacheInterface;
use Yiisoft\Config\
    {Config, ConfigInterface, ConfigPaths};
use Yiisoft\Di\
    {Container, ContainerConfig, NotFoundException};

use function dirname;

/**
 * Класс Enso
 *
 * @author Anton Sadovnikov <sadovnikoff@gmail.com>
 */
class Enso
{
    use Subject;  // attach "magic" properties getter/setter

    private ConfigInterface $_config;

    private ContainerInterface $_container;

    private LoggerInterface $_logger;

    private Relay $_relay;

    private CacheInterface $_cache;

    private EmitterInterface $_emitter;

    private mixed $_routing;

    /**
     *
     * @param ConfigInterface|null $config
     * @param ContainerInterface|null $container
     * @param Relay|null $relay
     * @param CacheInterface|null $cache
     * @param LoggerInterface|null $logger
     * @param EmitterInterface|null $emitter
     *
     * @throws InvalidConfigException
     * @throws NotInstantiableException
     * @throws ErrorException
     * @throws ContainerExceptionInterface
     */
    public function __construct(
        ?ConfigInterface $config = null,
        ?ContainerInterface $container = null,
        ?Relay $relay = null,
        ?CacheInterface $cache = null,
        ?LoggerInterface $logger = null,
        ?EmitterInterface $emitter = null,
    ) {
        $this->_config = $config ?? $this->createConfig();

        $this->_container = $container ?? $this->createContainer();

        $this->_relay = $relay ?? new Relay(
            queue: [
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
            ],
            resolver: null,
            context: $this->_container
        );

        $this->_emitter = $emitter ?? new CliEmitter();

        $this->getLogger()->info('Enso instantiated');
    }

    /**
     * @throws ErrorException
     */
    private function createConfig(): ConfigInterface
    {
        return new Config(
            paths: new ConfigPaths(
                rootPath: dirname(__FILE__, 2),
                configDirectory: 'config',
                vendorDirectory: 'vendor',
            ),
            environment: null,
            modifiers: [],
            paramsGroup: 'params'
        );
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->_config;
    }

    /**
     * @throws InvalidConfigException
     * @throws ErrorException
     */
    private function createContainer(): ContainerInterface
    {
        return new Container(
            ContainerConfig::create()
                ->withDefinitions(
                    $this->getConfig()->get('common')
                )
        );
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->_container;
    }

    public function get(string $id)
    {
        return $this
            ->getContainer()
            ->get($id);
    }

    /**
     * @return Relay
     */
    public function getRelay(): Relay
    {
        return $this->_relay;
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

    public function getEmitter(): EmitterInterface
    {
        return $this->_emitter;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->get(LoggerInterface::class);
    }

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface
    {
        return $this->get(CacheInterface::class);
    }

    public function getRoutingTree(string $configName = 'routing'): array
    {
        $context = &$this;

        return $this->_routing ?? $this->_routing = require (
                $this
                    ->get(Aliases::class)
                    ->get('@config')
                . "/{$configName}.php"
            );
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

}

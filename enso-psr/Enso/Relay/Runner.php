<?php
declare(strict_types = 1);
/**
 * Class Enso\Relay\Runner
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Closure;
use Psr\Http\Message\RequestInterface as PSRRequestInterface;
use Psr\Http\Message\ResponseInterface as PSRResponseInterface;

/**
 * Description of Runner
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Runner
{
    /** @var mixed[] */
    protected array $_queue;

    /** @var Closure */
    protected Closure $_resolver;

    /**
     *
     */
    public function __construct($queue, ?callable $resolver = null)
    {
        if (!is_iterable($queue))
        {
            throw new \TypeError('\$queue must be array or Traversable.');
        }

        if (!is_array($queue))
        {
            $queue = iterator_to_array($queue, true);
        }

        if (empty($queue))
        {
            throw new \Exception('$queue cannot be empty');
        }

        $this->_queue = $queue;

        $this->_resolver = $resolver ?? fn ($entry) => $entry;
    }

    /**
     *
     * @param PSRRequestInterface $request
     * @return Response
     * @throws \Exception
     */
    public function handle(PSRRequestInterface $request): PSRResponseInterface
    {
        $entry = current($this->_queue);

        if ($entry === false)
        {
            return [];
        }

        $middleware = call_user_func($this->_resolver, $entry);

        $nextEntry = next($this->_queue);

        if ($middleware instanceof MiddlewareInterface)
        {
            return $middleware->handle(request: $request, next: $this);
        }

        if ($middleware instanceof RequestHandler)
        {
            return $middleware->handle(request: $request);
        }

        if (is_callable(value: $middleware, callable_name: $callableName))
        {
            return $middleware($request, $this);
        }

        throw new \Exception(
            sprintf(
                'Invalid middleware queue entry: %s. Middleware must either be a callable or implement %s.', $middleware, MiddlewareInterface::class
            )
        );
    }

    /**
     * @return mixed[]
     */
    public function getQueue(): mixed
    {
        return $this->_queue;
    }

    /**
     *
     * @param Request $request
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        return $this->handle($request);
    }
}
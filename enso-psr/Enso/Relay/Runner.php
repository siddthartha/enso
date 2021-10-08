<?php
declare(strict_types = 1);
/**
 * Class Enso\Relay\Runner
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

/**
 * Description of Runner
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
class Runner
{
    /** @var mixed[] */
    protected $queue;

    /** @var callable */
    protected $resolver;

    /**
     *
     */
    public function __construct($queue, ?callable $resolver = null)
    {
        if (!is_iterable($queue))
        {
            throw new TypeError('\$queue must be array or Traversable.');
        }

        if (!is_array($queue))
        {
            $queue = iterator_to_array($queue, true);
        }

//        if (empty($queue))
//        {
//            throw new \Exception('$queue cannot be empty');
//        }

        $this->queue = $queue;

        if ($resolver === null)
        {
            $resolver = function ($entry)
            {
                return $entry;
            };
        }

        $this->resolver = $resolver;
    }

    public function handle(Request $request): Response
    {
        $entry = current($this->queue);
        $middleware = call_user_func($this->resolver, $entry);
        next($this->queue);

        if ($middleware instanceof MiddlewareInterface)
        {
            return $middleware->handle($request, $this);
        }

        if ($middleware instanceof RequestHandler)
        {
            return $middleware->handle($request);
        }

        if (is_callable($middleware))
        {
            return $middleware($request, $this);
        }

        throw new \Exception(
            sprintf(
                'Invalid middleware queue entry: %s. Middleware must either be callable or implement %s.', $middleware, MiddlewareInterface::class
            )
        );
    }

    public function __invoke(Request $request): Response
    {
        echo "Runner::__invoke\n";
        return $this->handle($request);
    }
}
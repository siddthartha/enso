<?php
declare(strict_types = 1);
/**
 * Class Enso\Relay\RequestHandler
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */

namespace Enso\Relay;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

use InvalidArgumentException;
use TypeError;

use function is_array;
use function is_iterable;
use function iterator_to_array;

/**
 * Description of RequestHandler
 *
 * @author Anton Sadovnikoff <sadovnikoff@gmail.com>
 */
abstract class RequestHandler
{
    /** @var mixed[] */
    protected $_queue;

    /** @var callable */
    protected $_resolver;

    /**
     * @param iterable<mixed> $queue    A queue of middleware entries.
     * @param callable        $resolver Converts a given queue entry to a callable or MiddlewareInterface instance.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function __construct($queue, ?callable $resolver = null)
    {
        if (!is_iterable($queue))
        {
            throw new TypeError('\$queue must be array or Traversable.');
        }

        if (!is_array($queue))
        {
            $queue = iterator_to_array($queue);
        }

        if (empty($queue))
        {
            throw new InvalidArgumentException('$queue cannot be empty');
        }

        $this->_queue = $queue;

        if ($resolver === null)
        {
            $resolver = function ($entry)
            {
                return $entry;
            };
        }

        $this->_resolver = $resolver;
    }

    /**
     * Handles the current entry in the middleware queue and advances.
     */
    abstract public function handle(RequestInterface $request): ResponseInterface;
}